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
use App\Exports\TransaksiTemplateExport;
use App\Models\HasilProsesAnggaran;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Barang;
use App\Models\DanaDarurat;
use Vinkla\Hashids\Facades\Hashids;
use App\Exports\TransaksiExport;
use App\Imports\TransaksiImportTest;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $baseQuery = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])
            ->where('id_user', $userId);

        // =====================
        // FILTER
        // =====================
        if ($request->filled('start_date')) {
            $baseQuery->whereDate('tgl_transaksi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $baseQuery->whereDate('tgl_transaksi', '<=', $request->end_date);
        }

        if ($request->filled('pemasukan')) {
            if (is_array($request->pemasukan)) {
                $baseQuery->whereIn('pemasukan', $request->pemasukan);
            }
            else {
                $baseQuery->where('pemasukan', $request->pemasukan);
            }
        }

        if ($request->filled('pengeluaran')) {
            if (is_array($request->pengeluaran)) {
                $baseQuery->whereIn('pengeluaran', $request->pengeluaran);
            }
            else {
                $baseQuery->where('pengeluaran', $request->pengeluaran);
            }
        }

        // =====================
        // SUMMARY UTAMA
        // =====================
        $totalPemasukan = (clone $baseQuery)->sum('nominal_pemasukan');
        $totalPengeluaran = (clone $baseQuery)->sum('nominal');
        $netIncome = $totalPemasukan - $totalPengeluaran;

        // =====================
        // SUMMARY DETAIL
        // =====================
        $summaryPemasukan = (clone $baseQuery)
            ->whereNotNull('pemasukan')
            ->selectRaw('pemasukan, SUM(nominal_pemasukan) as total')
            ->groupBy('pemasukan')
            ->with('pemasukanRelation')
            ->get();

        $summaryPengeluaran = (clone $baseQuery)
            ->whereNotNull('pengeluaran')
            ->selectRaw('pengeluaran, SUM(nominal) as total')
            ->groupBy('pengeluaran')
            ->with('pengeluaranRelation')
            ->get();

        // =====================
        // SORTING
        // =====================
        $allowedSorts = [
            'tgl_transaksi',
            'nominal_pemasukan',
            'nominal'
        ];

        $sort = $request->get('sort', 'tgl_transaksi');
        $direction = $request->get('direction', 'desc');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'tgl_transaksi';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        // =====================
        // PAGINATION
        // =====================
        $transaksi = (clone $baseQuery)
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('transaksi.index', [
            'transaksi' => $transaksi,

            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'netIncome' => $netIncome,

            'summaryPemasukan' => $summaryPemasukan,
            'summaryPengeluaran' => $summaryPengeluaran,

            // 🔽 WAJIB UNTUK FILTER
            'listPemasukan' => Pemasukan::where('id_user', $userId)->get(),
            'listPengeluaran' => Pengeluaran::where('id_user', $userId)->get(),
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        $userId = Auth::id();

        $query = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])
            ->where('id_user', $userId);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_transaksi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_transaksi', '<=', $request->end_date);
        }

        if ($request->filled('pemasukan')) {
            if (is_array($request->pemasukan)) {
                $query->whereIn('pemasukan', $request->pemasukan);
            }
            else {
                $query->where('pemasukan', $request->pemasukan);
            }
        }

        if ($request->filled('pengeluaran')) {
            if (is_array($request->pengeluaran)) {
                $query->whereIn('pengeluaran', $request->pengeluaran);
            }
            else {
                $query->where('pengeluaran', $request->pengeluaran);
            }
        }

        // sorting (whitelist)
        $allowedSorts = ['tgl_transaksi', 'nominal_pemasukan', 'nominal'];
        $sort = in_array($request->sort, $allowedSorts) ? $request->sort : 'tgl_transaksi';
        $direction = in_array($request->direction, ['asc', 'desc']) ? $request->direction : 'desc';

        return $query->orderBy($sort, $direction);
    }


    public function create()
    {
        $userId = Auth::id();

        $pemasukan = Pemasukan::where('id_user', $userId)->get();

        $pengeluaran = Pengeluaran::where('id_user', $userId)->get();

        $barang = Barang::where('id_user', $userId)->get();

        return view('transaksi.create', [
            'transaksi' => new Transaksi(),
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'barang' => $barang
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pemasukan' => 'nullable|string',
            'nominal_pemasukan' => 'nullable|numeric',
            'pengeluaran' => 'nullable|string',
            'nominal' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'barang_id' => 'nullable|exists:barang,id',
        ]);

        $validatedData['id_user'] = Auth::id();

        try {

            /** ======================================
             *  MAPPING PENGELUARAN (nama → id)
             * ====================================== */
            if (!empty($validatedData['pengeluaran'])) {
                $peng = Pengeluaran::where('nama', $validatedData['pengeluaran'])->first();

                if (!$peng) {
                    return back()->with('error', 'Jenis pengeluaran tidak ditemukan.');
                }

                $validatedData['pengeluaran'] = $peng->id;
            }

            /** ======================================
             *  MAPPING PEMASUKAN (nama → id)
             * ====================================== */
            if (!empty($validatedData['pemasukan'])) {
                $pem = Pemasukan::where('nama', $validatedData['pemasukan'])->first();

                if (!$pem) {
                    return back()->with('error', 'Jenis pemasukan tidak ditemukan.');
                }

                $validatedData['pemasukan'] = $pem->id;
            }

            /** ======================================
             * SET STATUS = 2 jika kategori berisi asset_list
             * ====================================== */
            if (in_array('asset_list', $request->kategori ?? [])) {
                $validatedData['status'] = 2;
            }

            /** ======================================
             * SIMPAN TRANSAKSI (HANYA SEKALI)
             * ====================================== */
            $transaksi = Transaksi::create($validatedData);

            /** ======================================
             * UPDATE HARGA BARANG (kalau ada barang_id)
             * ====================================== */
            if (!empty($validatedData['barang_id']) && $transaksi->nominal > 0) {
                Barang::where('id', $validatedData['barang_id'])
                    ->increment('harga', $transaksi->nominal);
            }

            /** ======================================
             * UPDATE DANA DARURAT
             * ====================================== */
            if (in_array('emergency_fund', $request->kategori ?? []) && $transaksi->nominal > 0) {

                $dana = DanaDarurat::firstOrCreate(
                ['id_user' => Auth::id()],
                ['total' => 0]
                );

                $dana->increment('total', $transaksi->nominal);
            }

            /** ======================================
             * UPDATE PROSES ANGGARAN (jika ada pengeluaran)
             * ====================================== */
            if (!empty($transaksi->pengeluaran) && $transaksi->nominal > 0) {

                $hasil = HasilProsesAnggaran::whereJsonContains(
                    'jenis_pengeluaran',
                    (string)$transaksi->pengeluaran
                )
                    ->where('tanggal_mulai', '<=', $transaksi->tgl_transaksi)
                    ->where('tanggal_selesai', '>=', $transaksi->tgl_transaksi)
                    ->first();

                if ($hasil) {
                    $hasil->increment('anggaran_yang_digunakan', floatval($transaksi->nominal));
                }
            }

            /** ======================================
             * REDIRECT
             * ====================================== */
            return redirect()->route('transaksi.index')
                ->with('success', 'Data Transaksi Berhasil Disimpan!');
        }
        catch (\Exception $e) {

            return back()->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }


    // public function edit($hash)
    // {

    //     $id = Hashids::decode($hash)[0] ?? null;
    //     abort_if(!$id, 404);

    //     $userId = Auth::id();

    //     // Pastikan transaksi milik user
    //     $transaksi = Transaksi::where('id', $id)
    //         ->where('id_user', $userId)
    //         ->firstOrFail();

    //     $pemasukan = Pemasukan::where('id_user', $userId)->get();
    //     $pengeluaran = Pengeluaran::where('id_user', $userId)->get();
    //     $barang = Barang::where('id_user', $userId)->get();

    //     return view('transaksi.edit', compact('transaksi', 'pemasukan', 'pengeluaran', 'barang'));
    // }

    public function edit($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $userId = Auth::id();

        $transaksi = Transaksi::where('id', $id)
            ->where('id_user', $userId)
            ->firstOrFail();

        $pemasukan = Pemasukan::where('id_user', $userId)->get();
        $pengeluaran = Pengeluaran::where('id_user', $userId)->get();
        $barang = Barang::where('id_user', $userId)->get();

        return view('transaksi.edit', compact(
            'transaksi',
            'pemasukan',
            'pengeluaran',
            'barang'
        ));
    }

    // public function update(Request $request, $id)
    // {
    //     $validatedData = $request->validate([
    //         'tgl_transaksi'      => 'required|date',
    //         'pemasukan'          => 'nullable|numeric',
    //         'nominal_pemasukan'  => 'nullable|numeric',
    //         'pengeluaran'        => 'nullable|numeric',
    //         'nominal'            => 'nullable|numeric',
    //         'keterangan'         => 'nullable|string|max:255',
    //     ]);

    //     try {
    //         // Update transaksi
    //         transaksi::where('id', $id)->update($validatedData);

    //         // Proses anggaran jika pengeluaran dan nominal ada
    //         if (!empty($validatedData['pengeluaran']) && $validatedData['nominal'] > 0) {
    //             $pengeluaranId = (string) $validatedData['pengeluaran'];
    //             $hasil = HasilProsesAnggaran::whereJsonContains('jenis_pengeluaran', $pengeluaranId)
    //                 ->where('tanggal_mulai', '<=', $validatedData['tgl_transaksi'])
    //                 ->where('tanggal_selesai', '>=', $validatedData['tgl_transaksi'])
    //                 ->first();

    //             if ($hasil) {
    //                 $hasil->increment('anggaran_yang_digunakan', floatval($validatedData['nominal']));
    //             }
    //         }

    //         return redirect()->route('transaksi.index')
    //             ->with('success', 'Berhasil Update Data Transaksi!');
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat update.',
    //             'error' => $e->getMessage(),
    //         ]);
    //     }
    // }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $validatedData = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pemasukan' => 'nullable|numeric',
            'nominal_pemasukan' => 'nullable|numeric',
            'pengeluaran' => 'nullable|numeric',
            'nominal' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Transaksi::where('id', $id)
            ->where('id_user', Auth::id())
            ->update($validatedData);

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Berhasil update transaksi');
    }


    public function show()
    {
    }

    // public function destroy($id)
    // {
    //     try {
    //         $transaksi = Transaksi::findOrFail($id);

    //         // Cast nominal untuk operasi pengurangan
    //         $nominal = floatval($transaksi->nominal);

    //         if ($nominal > 0 && !empty($transaksi->pengeluaran)) {
    //             $pengeluaran = Pengeluaran::where('nama', $transaksi->pengeluaran)->first();

    //             if ($pengeluaran) {
    //                 $pengeluaranId = (string) $pengeluaran->id;

    //                 $hasil = HasilProsesAnggaran::whereJsonContains('jenis_pengeluaran', $pengeluaranId)
    //                     ->where('tanggal_mulai', '<=', $transaksi->tgl_transaksi)
    //                     ->where('tanggal_selesai', '>=', $transaksi->tgl_transaksi)
    //                     ->first();

    //                 if ($hasil) {
    //                     // Kurangi anggaran_yang_digunakan, pastikan tidak negatif
    //                     $hasil->decrement('anggaran_yang_digunakan', $nominal);
    //                 }
    //             }
    //         }

    //         // Pengurangan ke tabel barang
    //         if (!empty($transaksi->barang_id) && $nominal > 0) {

    //             $barang = Barang::find($transaksi->barang_id);

    //             if ($barang) {

    //                 $hargaBaru = $barang->harga - $nominal;

    //                 if ($hargaBaru < 0) {
    //                     $hargaBaru = 0;
    //                 }

    //                 $barang->update([
    //                     'harga' => $hargaBaru
    //                 ]);
    //             }
    //         }

    //         // Hapus transaksi setelah update anggaran
    //         $transaksi->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Transaksi berhasil dihapus.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menghapus transaksi.',
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function destroy($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $transaksi = Transaksi::where('id', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // logic lama kamu (update anggaran, barang, dll)
        $transaksi->delete();

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }


    // public function cetak_pdf(Request $request)
    // {
    //     $userId = Auth::id();

    //     $start_date = $request->input('start_date');
    //     $end_date = $request->input('end_date');

    //     $formattedStartDate = Carbon::parse($start_date)->format('Y-m-d');
    //     $formattedEndDate = Carbon::parse($end_date)->format('Y-m-d');

    //     // $data = Transaksi::select('tgl_transaksi', 'pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan')
    //     $data = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])
    //         ->where('id_user', $userId)
    //         ->whereBetween('tgl_transaksi', [$formattedStartDate, $formattedEndDate])
    //         ->get();

    //     $pdf = PDF::loadView('Transaksi.pdf', [
    //         'transaksi' => $data,
    //         'start_date' => $formattedStartDate,
    //         'end_date' => $formattedEndDate
    //     ]);

    //     return $pdf->stream('Transaksi_Report.pdf');
    // }

    public function exportPdf(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $data = $query->get();

        $totalPemasukan = $data->sum('nominal_pemasukan');
        $totalPengeluaran = $data->sum('nominal');
        $netIncome = $totalPemasukan - $totalPengeluaran;

        $pdf = PDF::loadView('transaksi.export_pdf', [
            'transaksi' => $data,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'netIncome' => $netIncome,
            'filter' => $request->all(),
        ]);

        return $pdf->download('arus_kas.pdf');
    }

    // public function downloadExcel(Request $request)
    // {
    //     $userId = Auth::id();

    //     $request->validate([
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);

    //     $start_date = $request->input('start_date');
    //     $end_date = $request->input('end_date');

    //     $start_date_formatted = Carbon::parse($start_date)->format('Y-m-d');
    //     $end_date_formatted = Carbon::parse($end_date)->format('Y-m-d');

    //     $data = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])
    //         ->where('id_user', $userId)
    //         ->whereBetween('tgl_transaksi', [$start_date, $end_date])
    //         ->get();
    //     return Excel::download(new ExportExcel($data), 'Data_Transaksi_' . time() . '.xlsx');
    // }

    public function exportExcel(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $data = $query->get();

        return Excel::download(
            new TransaksiExport(
            $data,
            $data->sum('nominal_pemasukan'),
            $data->sum('nominal'),
            $data->sum('nominal_pemasukan') - $data->sum('nominal')
            ),
            'arus_kas.xlsx'
        );
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

    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx',
    //     ]);

    //     try {
    //         $importClass = 'App\\Imports\\TransaksiImport';

    //         if (!class_exists($importClass)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => "Import class not found: {$importClass}"
    //             ], 500);
    //         }

    //         $importInstance = app($importClass);
    //         Excel::import($importInstance, $request->file('file')->store('temp'));

    //         return response()->json(['success' => true], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false], 500);
    //     }
    // }


    public function importTest(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new TransaksiImportTest, $request->file('file'));

        return back()->with('success', 'Import berhasil');
    }


    public function toggleStatus($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = $transaksi->status == 1 ? 0 : 1;
        $transaksi->save();

        return response()->json(['success' => true, 'new_status' => $transaksi->status]);
    }

    public function bulkDelete(Request $request)
    {
        $userId = Auth::id();
        $ids = $request->ids;

        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['message' => 'No items selected'], 400);
        }

        // Validate and delete ensuring ownership
        $deleted = Transaksi::where('id_user', $userId)
            ->whereIn('id', $ids)
            ->delete();

        return response()->json([
            'message' => $deleted . ' transactions deleted successfully',
            'deleted_count' => $deleted
        ]);
    }
}
