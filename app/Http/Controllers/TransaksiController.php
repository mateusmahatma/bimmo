<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\PDF;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel;
use Illuminate\Support\Facades\Auth;
use App\Imports\TransaksiImport;
use App\Exports\TransaksiTemplateExport;
use App\Models\HasilProsesAnggaran;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $userId = Auth::id();

            $data = Transaksi::with(['pengeluaran', 'pemasukan'])->where('id_user', $userId);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

                $data = $data->whereBetween('tgl_transaksi', [$startDate, $endDate]);
            }

            if ($request->filled('pemasukan')) {
                $data = $data->where('pemasukan', $request->pemasukan);
            }

            if ($request->filled('pengeluaran')) {
                $data = $data->where('pengeluaran', $request->pengeluaran);
            }

            $totalPemasukan = $data->sum('nominal_pemasukan');
            $totalPengeluaran = $data->sum('nominal');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($request) {
                    return view('transaksi.tombol')->with('request', $request);
                })
                ->with('totalPemasukan', $totalPemasukan)
                ->with('totalPengeluaran', $totalPengeluaran)
                ->toJson();
        } else {
            return view('transaksi.index', [
                'transaksi' => Transaksi::where('id_user', Auth::id())->get(),
                'pemasukan' => Pemasukan::where('id_user', Auth::id())->get(),
                'pengeluaran' => Pengeluaran::where('id_user', Auth::id())->get(),
            ])->with('message', 'Pastikan format tanggal yang Anda kirimkan adalah YYYY-MM-DD.');
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tgl_transaksi'      => 'required|date',
            'pemasukan'          => 'nullable|string',
            'nominal_pemasukan'  => 'nullable|numeric',
            'pengeluaran'        => 'nullable|string',
            'nominal'            => 'nullable|numeric',
            'keterangan'         => 'nullable|string',
        ]);

        $validatedData['id_user'] = Auth::id();

        try {
            // Mapping nama pengeluaran menjadi ID
            if (!empty($validatedData['pengeluaran'])) {
                $pengeluaran = Pengeluaran::where('nama', $validatedData['pengeluaran'])->first();

                if (!$pengeluaran) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis pengeluaran tidak ditemukan.',
                    ]);
                }

                $validatedData['pengeluaran'] = $pengeluaran->id;
            }

            // Mapping nama pemasukan menjadi ID
            if (!empty($validatedData['pemasukan'])) {
                $pemasukan = Pemasukan::where('nama', $validatedData['pemasukan'])->first();

                if (!$pemasukan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis pemasukan tidak ditemukan.',
                    ]);
                }

                $validatedData['pemasukan'] = $pemasukan->id;
            }

            // Simpan transaksi
            $transaksi = Transaksi::create($validatedData);

            // Proses anggaran jika ada nominal pengeluaran dan ID pengeluaran
            if (!empty($transaksi->pengeluaran) && $transaksi->nominal > 0) {
                $pengeluaranId = (string) $transaksi->pengeluaran;

                $hasil = HasilProsesAnggaran::whereJsonContains('jenis_pengeluaran', $pengeluaranId)
                    ->where('tanggal_mulai', '<=', $transaksi->tgl_transaksi)
                    ->where('tanggal_selesai', '>=', $transaksi->tgl_transaksi)
                    ->first();

                if ($hasil) {
                    $hasil->increment('anggaran_yang_digunakan', floatval($transaksi->nominal));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil disimpan.',
                'redirect' => url('/transaksi')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $data = transaksi::where('id', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'tgl_transaksi'      => 'required|date',
            'pemasukan'          => 'nullable|numeric',
            'nominal_pemasukan'  => 'nullable|numeric',
            'pengeluaran'        => 'nullable|numeric',
            'nominal'            => 'nullable|numeric',
            'keterangan'         => 'nullable|string|max:255',
        ]);

        try {
            // Update transaksi
            transaksi::where('id', $id)->update($validatedData);

            // Proses anggaran jika pengeluaran dan nominal ada
            if (!empty($validatedData['pengeluaran']) && $validatedData['nominal'] > 0) {
                $pengeluaranId = (string) $validatedData['pengeluaran'];
                $hasil = HasilProsesAnggaran::whereJsonContains('jenis_pengeluaran', $pengeluaranId)
                    ->where('tanggal_mulai', '<=', $validatedData['tgl_transaksi'])
                    ->where('tanggal_selesai', '>=', $validatedData['tgl_transaksi'])
                    ->first();

                if ($hasil) {
                    $hasil->increment('anggaran_yang_digunakan', floatval($validatedData['nominal']));
                }
            }

            return response()->json(['success' => "Berhasil melakukan update data"]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function show() {}

    public function destroy($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);

            // Cast nominal untuk operasi pengurangan
            $nominal = floatval($transaksi->nominal);

            if ($nominal > 0 && !empty($transaksi->pengeluaran)) {
                $pengeluaran = Pengeluaran::where('nama', $transaksi->pengeluaran)->first();

                if ($pengeluaran) {
                    $pengeluaranId = (string) $pengeluaran->id;

                    $hasil = HasilProsesAnggaran::whereJsonContains('jenis_pengeluaran', $pengeluaranId)
                        ->where('tanggal_mulai', '<=', $transaksi->tgl_transaksi)
                        ->where('tanggal_selesai', '>=', $transaksi->tgl_transaksi)
                        ->first();

                    if ($hasil) {
                        // Kurangi anggaran_yang_digunakan, pastikan tidak negatif
                        $hasil->decrement('anggaran_yang_digunakan', $nominal);
                    }
                }
            }

            // Hapus transaksi setelah update anggaran
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cetak_pdf(Request $request)
    {
        $userId = Auth::id();

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $formattedStartDate = Carbon::parse($start_date)->format('Y-m-d');
        $formattedEndDate = Carbon::parse($end_date)->format('Y-m-d');

        $data = Transaksi::select('tgl_transaksi', 'pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan')
            ->where('id_user', $userId)
            ->whereBetween('tgl_transaksi', [$formattedStartDate, $formattedEndDate])
            ->get();

        $pdf = PDF::loadView('Transaksi.pdf', [
            'transaksi' => $data,
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate
        ]);

        return $pdf->stream('Transaksi_Report.pdf');
    }

    public function downloadExcel(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $start_date_formatted = Carbon::parse($start_date)->format('Y-m-d');
        $end_date_formatted = Carbon::parse($end_date)->format('Y-m-d');

        $data = Transaksi::select('tgl_transaksi', 'pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan')
            ->where('id_user', $userId)
            ->whereBetween('tgl_transaksi', [$start_date_formatted, $end_date_formatted])
            ->get();

        return Excel::download(new ExportExcel($data), 'Data_Transaksi_' . time() . '.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $path = $request->file('file')->getRealPath();
        $data = Excel::toCollection(null, $path)[0];

        $data->each(function ($row) {
            $validator = Validator::make($row->toArray(), [
                'tgl_transaksi' => 'required|date',
                'pemasukan' => 'nullable|string',
                'nominal_pemasukan' => 'nullable|numeric',
                'pengeluaran' => 'nullable|string',
                'nominal' => 'nullable|numeric',
                'keterangan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return;
            }

            Transaksi::create([
                'tgl_transaksi' => Carbon::parse($row['tgl_transaksi'])->format('Y-m-d'),
                'pemasukan' => $row['pemasukan'],
                'nominal_pemasukan' => $row['nominal_pemasukan'],
                'pengeluaran' => $row['pengeluaran'],
                'nominal' => $row['nominal'],
                'keterangan' => $row['keterangan'],
            ]);
        });

        return redirect('/transaksi')->with('success', 'Data Transaksi Berhasil Diimpor');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'id' => 'required',
        ]);

        if ($request->file('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $fileName = basename($path);

            $transaksi = Transaksi::find($request->id);
            $transaksi->file = $fileName;
            $transaksi->save();

            return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    public function downloadTemplate()
    {
        return Excel::download(new TransaksiTemplateExport, 'template_transaksi.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        try {
            Excel::import(new TransaksiImport, $request->file('file')->store('temp'));
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
