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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Barang;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();

            $data = Transaksi::with(['pengeluaranRelation', 'pemasukanRelation'])
                ->where('id_user', $userId);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
                $data = $data->whereBetween('tgl_transaksi', [$startDate, $endDate]);
            }

            if ($request->filled('filter_pemasukan')) {
                $data = $data->whereIn('pemasukan', $request->filter_pemasukan);
            }

            if ($request->filled('filter_pengeluaran')) {
                $data = $data->whereIn('pengeluaran', $request->filter_pengeluaran);
            }


            $totalPemasukan = (clone $data)->where('status', 1)->sum('nominal_pemasukan');
            $totalPengeluaran = (clone $data)->where('status', 1)->sum('nominal');
            $netIncome = number_format($totalPemasukan - $totalPengeluaran, 2, '.', '');

            $data = $data->get();

            foreach ($data as $item) {
                $item->file_exists = !is_null($item->file) && Storage::disk('public')->exists('uploads/' . $item->file);
            }

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('pemasukan_nama', function ($row) {
                    return $row->pemasukanRelation?->nama ?? '-';
                })

                ->addColumn('pengeluaran_nama', function ($row) {
                    return $row->pengeluaranRelation?->nama ?? '-';
                })

                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-';
                })

                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-';
                })

                ->addColumn('aksi', function ($row) {
                    return view('transaksi.tombol', ['request' => $row]);
                })

                ->with('totalPemasukan', $totalPemasukan)
                ->with('totalPengeluaran', $totalPengeluaran)
                ->with('netIncome', $netIncome)

                ->toJson();
        }

        return view('transaksi.index', [
            'transaksi' => Transaksi::where('id_user', Auth::id())->get(),
            'pemasukan' => Pemasukan::where('id_user', Auth::id())->get(),
            'pengeluaran' => Pengeluaran::where('id_user', Auth::id())->get(),
        ])->with('message', 'Pastikan format tanggal yang Anda kirimkan adalah YYYY-MM-DD.');
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
            'barang_id'          => 'nullable|exists:barang,id',
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

            // Tambahkan status 2 jika asset list dicentang
            if (in_array('asset_list', $request->kategori ?? [])) {
                $validatedData['status'] = 2;
            }

            // Simpan transaksi
            $transaksi = Transaksi::create($validatedData);

            // Tambahkan nominal ke kolom harga barang (jika ada barang_id dan nominal)
            if (!empty($validatedData['barang_id']) && $transaksi->nominal > 0) {
                Barang::where('id', $validatedData['barang_id'])
                    ->increment('harga', $transaksi->nominal);
            }

            // Proses anggaran jika ada pengeluaran dan nominal
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

        $data = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])
            ->where('id_user', $userId)
            ->whereBetween('tgl_transaksi', [$start_date, $end_date])
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

        $transaksi = Transaksi::findOrFail($request->id);

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if (!empty($transaksi->file) && Storage::disk('public')->exists('uploads/' . $transaksi->file)) {
                Storage::disk('public')->delete('uploads/' . $transaksi->file);
            }

            // Buat nama file baru dengan ekstensi
            $ext = $request->file('file')->getClientOriginalExtension();
            $fileName = Str::random(40) . '.' . $ext;

            // Simpan file ke disk 'public/uploads'
            $request->file('file')->storeAs('uploads', $fileName, 'public');

            // Update database
            $transaksi->file = $fileName;
            $transaksi->save();

            return response()->json(['success' => true, 'message' => 'File uploaded & replaced successfully']);
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

    public function toggleStatus($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = $transaksi->status == 1 ? 0 : 1;
        $transaksi->save();

        return response()->json(['success' => true, 'new_status' => $transaksi->status]);
    }
}
