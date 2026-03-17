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
use App\Models\Dompet;
use Vinkla\Hashids\Facades\Hashids;
use App\Exports\TransaksiExport;
use App\Imports\TransaksiImportTest;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionExportMail;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Default to this month if no filter is applied
        if (!$request->filled('start_date') && !$request->filled('end_date') && !$request->ajax()) {
            $request->merge([
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
            ]);
        }

        // 1. Fetch ALL records using basic SQL filters (Date, User)
        $baseQuery = $this->buildFilteredQuery($request, true); // true = skip sorting in SQL
        $allRecords = $baseQuery->get();

        // 2. Apply PHP-side filtering for encrypted fields
        $filteredTransactions = $this->applyPhpFilters($request, $allRecords);

        // 3. Stats and Aggregations on Filtered Collection
        $totalPemasukan = $filteredTransactions->sum(fn($t) => (float)$t->nominal_pemasukan);
        $totalPengeluaran = $filteredTransactions->sum(fn($t) => (float)$t->nominal);
        $netIncome = $totalPemasukan - $totalPengeluaran;

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $diffInDays = $startDate->diffInDays($endDate) + 1;

        $avgDailyPengeluaran = $totalPengeluaran / max(1, $diffInDays);
        $avgMonthlyPengeluaran = $avgDailyPengeluaran * 30;
        $dateRange = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

        // 4. Handle AJAX Response (Datatables/Custom)
        if ($request->ajax()) {
            $stats = [
                'totalPemasukan' => $totalPemasukan,
                'totalPengeluaran' => $totalPengeluaran,
                'netIncome' => $netIncome,
                'avgDailyPengeluaran' => $avgDailyPengeluaran,
                'avgMonthlyPengeluaran' => $avgMonthlyPengeluaran,
                'dateRange' => $dateRange
            ];

            // Summary Details
            $summaryPemasukan = $filteredTransactions->whereNotNull('pemasukan')
                ->groupBy('pemasukan')
                ->map(function ($items, $key) {
                    return (object)[
                        'pemasukan' => $key,
                        'total' => $items->sum(fn($t) => (float)$t->nominal_pemasukan),
                        'pemasukanRelation' => $items->first()->pemasukanRelation
                    ];
                })
                ->sortByDesc('total')->values();

            $summaryPengeluaran = $filteredTransactions->whereNotNull('pengeluaran')
                ->groupBy('pengeluaran')
                ->map(function ($items, $key) {
                    return (object)[
                        'pengeluaran' => $key,
                        'total' => $items->sum(fn($t) => (float)$t->nominal),
                        'pengeluaranRelation' => $items->first()->pengeluaranRelation
                    ];
                })
                ->sortByDesc('total')->values();

            // Render Modals
            $modalPemasukanHtml = $this->renderPemasukanModal($summaryPemasukan, $totalPemasukan);
            $modalPengeluaranHtml = $this->renderPengeluaranModal($summaryPengeluaran, $totalPengeluaran);

            // Manual Pagination for AJAX table
            $page = $request->get('page', 1);
            $perPage = 10;
            $items = $filteredTransactions->forPage($page, $perPage)->values();
            $paginatedTransaksi = new \Illuminate\Pagination\LengthAwarePaginator($items, $filteredTransactions->count(), $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return response()->json([
                'html' => view('transaksi._table_list', ['transaksi' => $paginatedTransaksi])->render(),
                'stats' => $stats,
                'modal_pemasukan' => $modalPemasukanHtml,
                'modal_pengeluaran' => $modalPengeluaranHtml
            ]);
        }

        // 5. Initial Load - Manual Pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        $items = $filteredTransactions->forPage($page, $perPage)->values();
        $transaksi = new \Illuminate\Pagination\LengthAwarePaginator($items, $filteredTransactions->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        $summaryPemasukan = $filteredTransactions->whereNotNull('pemasukan')
            ->groupBy('pemasukan')
            ->map(function ($items, $key) {
                return (object)[
                    'pemasukan' => $key,
                    'total' => $items->sum(fn($t) => (float)$t->nominal_pemasukan),
                    'pemasukanRelation' => $items->first()->pemasukanRelation
                ];
            })
            ->sortByDesc('total')->values();

        $summaryPengeluaran = $filteredTransactions->whereNotNull('pengeluaran')
            ->groupBy('pengeluaran')
            ->map(function ($items, $key) {
                return (object)[
                    'pengeluaran' => $key,
                    'total' => $items->sum(fn($t) => (float)$t->nominal),
                    'pengeluaranRelation' => $items->first()->pengeluaranRelation
                ];
            })
            ->sortByDesc('total')->values();

        return view('transaksi.index', [
            'transaksi' => $transaksi,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'netIncome' => $netIncome,
            'summaryPemasukan' => $summaryPemasukan,
            'summaryPengeluaran' => $summaryPengeluaran,
            'listPemasukan' => Pemasukan::where('id_user', $userId)->get(),
            'listPengeluaran' => Pengeluaran::where('id_user', $userId)->get(),
            'avgDailyPengeluaran' => $avgDailyPengeluaran,
            'avgMonthlyPengeluaran' => $avgMonthlyPengeluaran,
            'dateRange' => $dateRange
        ]);
    }

    private function applyPhpFilters(Request $request, $collection)
    {
        return $collection->filter(function ($t) use ($request) {
            // Filter by Pemasukan Category
            if ($request->filled('pemasukan')) {
                $pemasukanIds = is_array($request->pemasukan) ? $request->pemasukan : [$request->pemasukan];
                if (!in_array($t->pemasukan, $pemasukanIds)) return false;
            }

            // Filter by Pengeluaran Category
            if ($request->filled('pengeluaran')) {
                $pengeluaranIds = is_array($request->pengeluaran) ? $request->pengeluaran : [$request->pengeluaran];
                if (!in_array($t->pengeluaran, $pengeluaranIds)) return false;
            }

            // Filter by Search
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $pemasukanName = strtolower($t->pemasukanRelation->nama ?? '');
                $pengeluaranName = strtolower($t->pengeluaranRelation->nama ?? '');
                $keterangan = strtolower($t->keterangan ?? '');

                if (
                    strpos($pemasukanName, $search) === false &&
                    strpos($pengeluaranName, $search) === false &&
                    strpos($keterangan, $search) === false
                ) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private function buildFilteredQuery(Request $request, $skipSort = false)
    {
        $userId = Auth::id();
        $query = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])->where('id_user', $userId);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_transaksi', '<=', $request->end_date);
        }

        if (!$skipSort) {
            $query->orderBy('tgl_transaksi', 'desc');
        }

        return $query;
    }

    private function renderPemasukanModal($summary, $total)
    {
        if ($summary->isEmpty()) return '<li class="list-group-item text-center text-muted py-3">No data available</li>';
        $html = '';
        foreach ($summary as $row) {
            $percentage = $total > 0 ? ($row->total / $total) * 100 : 0;
            $html .= '<li class="list-group-item border-0 px-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium text-dark">' . ($row->pemasukanRelation->nama ?? 'Others') . '</span>
                            <span class="fw-bold text-success small">Rp ' . number_format($row->total, 0, ',', '.') . '</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: ' . $percentage . '%"></div>
                        </div>
                        <div class="text-end text-muted" style="font-size: 10px;">' . number_format($percentage, 1) . '%</div>
                    </li>';
        }
        return $html;
    }

    private function renderPengeluaranModal($summary, $total)
    {
        if ($summary->isEmpty()) return '<li class="list-group-item text-center text-muted py-3">No data available</li>';
        $html = '';
        foreach ($summary as $row) {
            $percentage = $total > 0 ? ($row->total / $total) * 100 : 0;
            $html .= '<li class="list-group-item border-0 px-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium text-dark">' . ($row->pengeluaranRelation->nama ?? 'Others') . '</span>
                            <span class="fw-bold text-danger small">Rp ' . number_format($row->total, 0, ',', '.') . '</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: ' . $percentage . '%"></div>
                        </div>
                        <div class="text-end text-muted" style="font-size: 10px;">' . number_format($percentage, 1) . '%</div>
                    </li>';
        }
        return $html;
    }

    public function create()
    {
        $userId = Auth::id();
        return view('transaksi.create', [
            'pemasukan' => Pemasukan::where('id_user', $userId)->get(),
            'pengeluaran' => Pengeluaran::where('id_user', $userId)->get(),
            'barang' => Barang::where('id_user', $userId)->get(),
            'dompet' => Dompet::where('id_user', $userId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pemasukan' => 'required_without:pengeluaran|required_with:nominal_pemasukan|nullable|string',
            'nominal_pemasukan' => 'required_with:pemasukan|nullable|numeric|min:0',
            'pengeluaran' => 'required_without:pemasukan|required_with:nominal|nullable|string',
            'nominal' => 'required_with:pengeluaran|nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'barang_id' => 'nullable|exists:barang,id',
            'dompet_id' => 'nullable|exists:dompet,id',
        ]);

        $validatedData['id_user'] = Auth::id();
        try {
            if (in_array('asset_list', $request->kategori ?? [])) $validatedData['status'] = 2;

            $transaksi = Transaksi::create($validatedData);

            if (!empty($validatedData['barang_id']) && $transaksi->nominal > 0) {
                Barang::where('id', $validatedData['barang_id'])->increment('harga', $transaksi->nominal);
            }

            if (in_array('emergency_fund', $request->kategori ?? []) && $transaksi->nominal > 0) {
                $dana = DanaDarurat::firstOrCreate(['id_user' => Auth::id()], ['total' => 0]);
                $dana->increment('total', $transaksi->nominal);
            }

            if (!empty($transaksi->pengeluaran) && $transaksi->nominal > 0) {
                $this->syncBudget($transaksi, true);
            }

            if ($transaksi->dompet_id) {
                $dompet = Dompet::find($transaksi->dompet_id);
                if ($dompet) {
                    if ($transaksi->nominal_pemasukan > 0) {
                        $dompet->saldo = (float)$dompet->saldo + (float)$transaksi->nominal_pemasukan;
                    } else if ($transaksi->nominal > 0) {
                        $dompet->saldo = (float)$dompet->saldo - (float)$transaksi->nominal;
                    }
                    $dompet->save();
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Transaksi Berhasil Disimpan!',
                    'redirect_url' => route('transaksi.index'),
                    'redirect_name' => __('Transactions')
                ]);
            }

            return redirect()->route('transaksi.index')->with('success', 'Data Transaksi Berhasil Disimpan!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi error: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }

    public function show($hash)
    {
        return redirect()->route('transaksi.edit', $hash);
    }
    public function edit($hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        $userId = Auth::id();
        $transaksi = Transaksi::where('id', $id)->where('id_user', $userId)->firstOrFail();
        return view('transaksi.edit', [
            'transaksi' => $transaksi,
            'pemasukan' => Pemasukan::where('id_user', $userId)->get(),
            'pengeluaran' => Pengeluaran::where('id_user', $userId)->get(),
            'barang' => Barang::where('id_user', $userId)->get(),
            'dompet' => Dompet::where('id_user', $userId)->get(),
        ]);
    }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        $validatedData = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pemasukan' => 'required_without:pengeluaran|required_with:nominal_pemasukan|nullable|numeric',
            'nominal_pemasukan' => 'required_with:pemasukan|nullable|numeric|min:0',
            'pengeluaran' => 'required_without:pemasukan|required_with:nominal|nullable|numeric',
            'nominal' => 'required_with:pengeluaran|nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'dompet_id' => 'nullable|exists:dompet,id',
        ]);

        $transaksi = Transaksi::where('id', $id)->where('id_user', Auth::id())->firstOrFail();

        try {
            // Revert old wallet balance
            if ($transaksi->dompet_id) {
                $oldDompet = Dompet::find($transaksi->dompet_id);
                if ($oldDompet) {
                    if ($transaksi->nominal_pemasukan > 0) {
                        $oldDompet->saldo = (float)$oldDompet->saldo - (float)$transaksi->nominal_pemasukan;
                    } else if ($transaksi->nominal > 0) {
                        $oldDompet->saldo = (float)$oldDompet->saldo + (float)$transaksi->nominal;
                    }
                    $oldDompet->save();
                }
            }

            // Revert old budget impact
            $this->syncBudget($transaksi, false);

            $transaksi->update($validatedData);

            // Apply new wallet balance
            if ($transaksi->dompet_id) {
                $newDompet = Dompet::find($transaksi->dompet_id);
                if ($newDompet) {
                    if ($transaksi->nominal_pemasukan > 0) {
                        $newDompet->saldo = (float)$newDompet->saldo + (float)$transaksi->nominal_pemasukan;
                    } else if ($transaksi->nominal > 0) {
                        $newDompet->saldo = (float)$newDompet->saldo - (float)$transaksi->nominal;
                    }
                    $newDompet->save();
                }
            }

            // Apply new budget impact
            $this->syncBudget($transaksi, true);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Transaksi Berhasil Diperbarui!',
                    'redirect_url' => route('transaksi.index'),
                    'redirect_name' => __('Transactions')
                ]);
            }
            return redirect()->route('transaksi.index')->with('success', 'Data Transaksi Berhasil Diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }

    public function destroy($hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        $transaksi = Transaksi::where('id', $id)->where('id_user', Auth::id())->firstOrFail();

        // Revert wallet balance before deleting
        if ($transaksi->dompet_id) {
            $dompet = Dompet::find($transaksi->dompet_id);
            if ($dompet) {
                if ($transaksi->nominal_pemasukan > 0) {
                    $dompet->saldo = (float)$dompet->saldo - (float)$transaksi->nominal_pemasukan;
                } else if ($transaksi->nominal > 0) {
                    $dompet->saldo = (float)$dompet->saldo + (float)$transaksi->nominal;
                }
                $dompet->save();
            }
        }

        // Revert budget impact before deleting
        $this->syncBudget($transaksi, false);

        $transaksi->delete();

        if (request()->ajax()) return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus']);
        return redirect()->route('transaksi.index')->with('success', 'Data Transaksi Berhasil Dihapus!');
    }

    public function exportPdf(Request $request)
    {
        ini_set('max_execution_time', 300);
        $statsQuery = $this->buildFilteredQuery($request, true);
        $allRecords = $statsQuery->get();
        $data = $this->applyPhpFilters($request, $allRecords);

        $totalPemasukan = $data->sum(fn($t) => (float)$t->nominal_pemasukan);
        $totalPengeluaran = $data->sum(fn($t) => (float)$t->nominal);

        $pdf = PDF::loadView('transaksi.export_pdf', [
            'transaksi' => $data,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'netIncome' => $totalPemasukan - $totalPengeluaran,
            'filter' => $request->all(),
        ])->setPaper('A4', 'landscape');

        return $pdf->download('arus_kas.pdf');
    }

    public function exportExcel(Request $request)
    {
        $statsQuery = $this->buildFilteredQuery($request, true);
        $allRecords = $statsQuery->get();
        $data = $this->applyPhpFilters($request, $allRecords);

        return Excel::download(new TransaksiExport($data, $data->sum(fn($t)=>(float)$t->nominal_pemasukan), $data->sum(fn($t)=>(float)$t->nominal), $data->sum(fn($t)=>(float)$t->nominal_pemasukan)-$data->sum(fn($t)=>(float)$t->nominal)), 'arus_kas.xlsx');
    }

    public function emailExcel(Request $request)
    {
        $statsQuery = $this->buildFilteredQuery($request, true);
        $allRecords = $statsQuery->get();
        $data = $this->applyPhpFilters($request, $allRecords);

        $excelData = Excel::raw(new TransaksiExport($data, $data->sum(fn($t)=>(float)$t->nominal_pemasukan), $data->sum(fn($t)=>(float)$t->nominal), $data->sum(fn($t)=>(float)$t->nominal_pemasukan)-$data->sum(fn($t)=>(float)$t->nominal)), \Maatwebsite\Excel\Excel::XLSX);

        $recipientEmail = $request->email ?? Auth::user()->email;
        Mail::to($recipientEmail)->send(new TransactionExportMail($excelData, 'Arus_Kas_BIMMO.xlsx'));
        return back()->with('success', 'Data transaksi berhasil dikirim ke email ' . $recipientEmail);
    }

    public function upload(Request $request)
    {
        $request->validate(['file'=>'required|file|mimes:jpg,jpeg,png,pdf|max:2048', 'id'=>'required|exists:transaksi,id']);
        $transaksi = Transaksi::findOrFail($request->id);
        if ($request->hasFile('file')) {
            if (!empty($transaksi->file) && Storage::disk('public')->exists('uploads/'.$transaksi->file)) Storage::disk('public')->delete('uploads/'.$transaksi->file);
            $fileName = Str::random(40).'.'.$request->file('file')->getClientOriginalExtension();
            $request->file('file')->storeAs('uploads', $fileName, 'public');
            $transaksi->file = $fileName;
            $transaksi->save();
            return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
        }
        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    public function deleteFile($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        if (!empty($transaksi->file)) {
            if (Storage::disk('public')->exists('uploads/'.$transaksi->file)) Storage::disk('public')->delete('uploads/'.$transaksi->file);
            $transaksi->file = null;
            $transaksi->save();
            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'No file to delete']);
    }

    public function downloadTemplate()
    {
        return Excel::download(new TransaksiTemplateExport, 'template_transaksi.xlsx');
    }

    public function importTest(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        $sheets = Excel::toCollection(new TransaksiImportTest, $request->file('file'));
        $processedCount = 0;
        foreach ($sheets as $rows) {
            if ($rows->isEmpty()) continue;
            $keys = array_keys($rows->first()->toArray());
            if (!in_array('tanggal_transaksi', $keys) && !in_array('tgl_transaksi', $keys)) continue;

            foreach ($rows as $row) {
                $tglRaw = $row['tanggal_transaksi'] ?? $row['tgl_transaksi'] ?? null;
                if (empty($tglRaw)) continue;

                // Robust column detection for 'keterangan'
                $keterangan = $row['keterangan'] ?? $row['description'] ?? $row['deskripsi'] ?? $row['remarks'] ?? $row['catatan'] ?? null;

                try {
                    $tgl = is_numeric($tglRaw) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglRaw))->format('Y-m-d') : Carbon::parse($tglRaw)->format('Y-m-d');
                } catch (\Exception $e) { continue; }

                if (($row['nominal_pemasukan'] ?? 0) > 0) {
                    Transaksi::create([
                        'tgl_transaksi' => $tgl,
                        'pemasukan' => $row['jenis_pemasukan'] ?? $row['pemasukan'] ?? null,
                        'nominal_pemasukan' => $row['nominal_pemasukan'],
                        'keterangan' => $keterangan,
                        'id_user' => Auth::id()
                    ]);
                    $processedCount++;
                }
                if (($row['nominal_pengeluaran'] ?? $row['nominal'] ?? 0) > 0) {
                    Transaksi::create([
                        'tgl_transaksi' => $tgl,
                        'pengeluaran' => $row['jenis_pengeluaran'] ?? $row['pengeluaran'] ?? null,
                        'nominal' => $row['nominal_pengeluaran'] ?? $row['nominal'],
                        'keterangan' => $keterangan,
                        'id_user' => Auth::id()
                    ]);
                    $processedCount++;
                }
            }
            break;
        }
        return back()->with('success', 'Import berhasil! ' . $processedCount . ' data ditambahkan.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids) || count($ids) === 0) return response()->json(['message' => 'No items selected'], 400);
        
        $transactions = Transaksi::where('id_user', Auth::id())->whereIn('id', $ids)->get();
        $deletedCount = 0;
        foreach($transactions as $transaksi) {
            // Revert wallet balance before deleting
            if ($transaksi->dompet_id) {
                $dompet = Dompet::find($transaksi->dompet_id);
                if ($dompet) {
                    if ($transaksi->nominal_pemasukan > 0) {
                        $dompet->saldo = (float)$dompet->saldo - (float)$transaksi->nominal_pemasukan;
                    } else if ($transaksi->nominal > 0) {
                        $dompet->saldo = (float)$dompet->saldo + (float)$transaksi->nominal;
                    }
                    $dompet->save();
                }
            }
            // Revert budget impact
            $this->syncBudget($transaksi, false);
            
            $transaksi->delete();
            $deletedCount++;
        }
        return response()->json(['message' => $deletedCount . ' transactions deleted successfully']);
    }

    private function syncBudget(Transaksi $transaksi, $isIncrement = true)
    {
        if (empty($transaksi->pengeluaran) || (float)$transaksi->nominal <= 0) return;

        $hasil = HasilProsesAnggaran::where('id_user', $transaksi->id_user)
            ->whereJsonContains('jenis_pengeluaran', (string)$transaksi->pengeluaran)
            ->where('tanggal_mulai', '<=', $transaksi->tgl_transaksi)
            ->where('tanggal_selesai', '>=', $transaksi->tgl_transaksi)
            ->first();

        if ($hasil) {
            $currentUsed = (float)$hasil->getRawOriginal('anggaran_yang_digunakan') ? (float)$hasil->anggaran_yang_digunakan : 0;
            $nominal = (float)$transaksi->nominal;
            
            if ($isIncrement) {
                $hasil->anggaran_yang_digunakan = $currentUsed + $nominal;
            } else {
                $hasil->anggaran_yang_digunakan = max(0, $currentUsed - $nominal);
            }
            $hasil->save();
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