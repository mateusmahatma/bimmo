<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Auth;
use App\Models\HasilProsesAnggaran;
use Yajra\DataTables\DataTables;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Vinkla\Hashids\Facades\Hashids;

class FinancialCalculatorController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = HasilProsesAnggaran::where('id_user', $userId);

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_anggaran', 'like', "%{$search}%")
                    ->orWhere('tanggal_mulai', 'like', "%{$search}%")
                    ->orWhere('tanggal_selesai', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $hasilProses = $query->paginate(10)->withQueryString();

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return view('kalkulator._table_list', compact('hasilProses'))->render();
        }

        $pemasukans = \App\Models\Pemasukan::where('id_user', $userId)->get();

        return view('kalkulator.index', compact('hasilProses', 'pemasukans'));
    }

    public function store(Request $request)
    {
        $request->validate(['id_pemasukan' => 'required|array', 'tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date']);
        $userId = Auth::id();
        $idPemasukans = $request->input('id_pemasukan');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        // Sum income transactions in range
        $allIncomesInRange = Transaksi::where('id_user', $userId)
            ->whereBetween('tgl_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->get();
        
        $totalIncome = $allIncomesInRange->filter(function($t) use ($idPemasukans) {
            return in_array((string)$t->pemasukan, array_map('strval', $idPemasukans));
        })->sum(fn($t) => (float)($t->nominal_pemasukan ?? 0));

        $anggarans = Anggaran::where('id_user', $userId)->whereNotNull('id_pengeluaran')->get();
        foreach ($anggarans as $anggaran) {
            $jenisPengeluaran = is_array($anggaran->id_pengeluaran) ? $anggaran->id_pengeluaran : json_decode($anggaran->id_pengeluaran, true);
            if (!is_array($jenisPengeluaran)) $jenisPengeluaran = [$anggaran->id_pengeluaran];

            $allTrxInRange = Transaksi::where('id_user', $userId)->whereBetween('tgl_transaksi', [$tanggal_mulai, $tanggal_selesai])->get();
            $totalTransaksi = $allTrxInRange->filter(fn($t) => in_array((string)$t->pengeluaran, array_map('strval', $jenisPengeluaran)))->sum(fn($t) => (float)$t->nominal);

            $nominal = ($anggaran->persentase_anggaran / 100) * $totalIncome;

            HasilProsesAnggaran::create([
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'nama_anggaran' => $anggaran->nama_anggaran,
                'jenis_pengeluaran' => $anggaran->id_pengeluaran,
                'jenis_pemasukan' => $idPemasukans,
                'persentase_anggaran' => $anggaran->persentase_anggaran,
                'nominal_anggaran' => $nominal,
                'anggaran_yang_digunakan' => $totalTransaksi,
                'id_user' => $userId
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Data anggaran berhasil diproses.', 'redirect' => url('/kalkulator')]);
    }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {

            $prosesAnggaran = HasilProsesAnggaran::find($id);
            if (!$prosesAnggaran) return response()->json(['error' => 'Data tidak ditemukan'], 404);

            // Sync with original Anggaran if exists
            $originalAnggaran = Anggaran::where('id_user', $prosesAnggaran->id_user)
                ->where('nama_anggaran', $prosesAnggaran->nama_anggaran)
                ->first();

            if ($originalAnggaran) {
                $prosesAnggaran->persentase_anggaran = $originalAnggaran->persentase_anggaran;
                $prosesAnggaran->jenis_pengeluaran = $originalAnggaran->id_pengeluaran;
            }

            // Recalculate Income (Budget)
            $idPemasukans = $prosesAnggaran->jenis_pemasukan;
            if (empty($idPemasukans)) {
                // For old records, fallback to all user income categories
                $idPemasukans = Pemasukan::where('id_user', $prosesAnggaran->id_user)->pluck('id')->toArray();
            }

            if (!empty($idPemasukans)) {
                if (!is_array($idPemasukans)) $idPemasukans = json_decode((string)$idPemasukans, true) ?? [$idPemasukans];

                $allIncomesInRange = Transaksi::where('id_user', $prosesAnggaran->id_user)
                    ->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])
                    ->get();
                
                $totalIncome = $allIncomesInRange->filter(function($t) use ($idPemasukans) {
                    return in_array((string)$t->pemasukan, array_map('strval', $idPemasukans));
                })->sum(function($t) {
                    $val = (string)($t->nominal_pemasukan ?? '0');
                    $cleanVal = str_replace(['.', ','], ['', '.'], $val);
                    return (float)$cleanVal;
                });

                $prosesAnggaran->nominal_anggaran = ($prosesAnggaran->persentase_anggaran / 100) * $totalIncome;
            }

            // Recalculate Expenses (Used)
            $jenisPengeluaran = $prosesAnggaran->jenis_pengeluaran;
            if (!is_array($jenisPengeluaran)) {
                if (is_string($jenisPengeluaran)) {
                    $decoded = json_decode($jenisPengeluaran, true);
                    $jenisPengeluaran = is_array($decoded) ? $decoded : [$jenisPengeluaran];
                } else {
                    $jenisPengeluaran = [$jenisPengeluaran];
                }
            }

            $allTrxInRange = Transaksi::where('id_user', $prosesAnggaran->id_user)->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])->get();
            $totalTransaksi = $allTrxInRange->filter(fn($t) => in_array((string)$t->pengeluaran, array_map('strval', $jenisPengeluaran)))->sum(fn($t) => (float)$t->nominal);

            $prosesAnggaran->anggaran_yang_digunakan = $totalTransaksi;
            $prosesAnggaran->save();

            return response()->json([
                'id' => Hashids::encode($prosesAnggaran->id_proses_anggaran),
                'persentase_anggaran' => number_format($prosesAnggaran->persentase_anggaran, 0),
                'nama_jenis_pengeluaran' => $prosesAnggaran->nama_jenis_pengeluaran,
                'nominal_anggaran_terkini' => number_format($prosesAnggaran->nominal_anggaran, 0, ',', '.'),
                'anggaran_digunakan_terkini' => number_format($totalTransaksi, 0, ',', '.'),
                'sisa_anggaran' => number_format(floatval($prosesAnggaran->nominal_anggaran) - $totalTransaksi, 0, ',', '.')
            ]);
        }
    }

    public function bulkSync(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) return response()->json(['message' => 'No data selected'], 400);
        $decodedIds = array_filter(array_map(fn($hash) => Hashids::decode($hash)[0] ?? null, $ids));
        if (empty($decodedIds)) return response()->json(['message' => 'Invalid data'], 400);

        $count = 0;
        foreach ($decodedIds as $id) {
            $prosesAnggaran = HasilProsesAnggaran::find($id);
            if (!$prosesAnggaran || $prosesAnggaran->id_user !== Auth::id()) continue;

            // Sync with original Anggaran if exists
            $originalAnggaran = Anggaran::where('id_user', $prosesAnggaran->id_user)
                ->where('nama_anggaran', $prosesAnggaran->nama_anggaran)
                ->first();

            if ($originalAnggaran) {
                $prosesAnggaran->persentase_anggaran = $originalAnggaran->persentase_anggaran;
                $prosesAnggaran->jenis_pengeluaran = $originalAnggaran->id_pengeluaran;
            }

            // Recalculate Income (Budget)
            $idPemasukans = $prosesAnggaran->jenis_pemasukan;
            if (empty($idPemasukans)) {
                // Fallback for old records
                $idPemasukans = Pemasukan::where('id_user', $prosesAnggaran->id_user)->pluck('id')->toArray();
            }

            if (!empty($idPemasukans)) {
                if (!is_array($idPemasukans)) $idPemasukans = json_decode((string)$idPemasukans, true) ?? [$idPemasukans];

                $allIncomesInRange = Transaksi::where('id_user', $prosesAnggaran->id_user)
                    ->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])
                    ->get();
                
                $totalIncome = $allIncomesInRange->filter(function($t) use ($idPemasukans) {
                    return in_array((string)$t->pemasukan, array_map('strval', $idPemasukans));
                })->sum(function($t) {
                    $val = (string)($t->nominal_pemasukan ?? '0');
                    $cleanVal = str_replace(['.', ','], ['', '.'], $val);
                    return (float)$cleanVal;
                });

                $prosesAnggaran->nominal_anggaran = ($prosesAnggaran->persentase_anggaran / 100) * $totalIncome;
            }

            // Recalculate Expenses (Used)
            $jenisPengeluaran = $prosesAnggaran->jenis_pengeluaran;
            if (!is_array($jenisPengeluaran)) {
                if (is_string($jenisPengeluaran)) {
                    $decoded = json_decode($jenisPengeluaran, true);
                    $jenisPengeluaran = is_array($decoded) ? $decoded : [$jenisPengeluaran];
                } else {
                    $jenisPengeluaran = [$jenisPengeluaran];
                }
            }

            $allTrxInRange = Transaksi::where('id_user', $prosesAnggaran->id_user)->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])->get();
            $totalTransaksi = $allTrxInRange->filter(fn($t) => in_array((string)$t->pengeluaran, array_map('strval', $jenisPengeluaran)))->sum(fn($t) => (float)$t->nominal);

            $prosesAnggaran->anggaran_yang_digunakan = $totalTransaksi;
            $prosesAnggaran->save();
            $count++;
        }

        return $count > 0 ? response()->json(['message' => "$count data berhasil disinkronisasi"]) : response()->json(['message' => 'Gagal menyinkronkan data atau data tidak ditemukan'], 404);
    }

    public function calculate(Request $request)
    {
        $totalIncome = (float)$request->input('monthly_income') + (float)$request->input('additional_income');
        $anggarans = Anggaran::where('id_user', Auth::id())->get();
        $budgetAllocations = [];
        foreach ($anggarans as $anggaran) {
            $budgetAllocations[] = ['nama_anggaran' => $anggaran->nama_anggaran, 'persentase_anggaran' => $anggaran->persentase_anggaran, 'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome];
        }
        $totalBudget = array_sum(array_column($budgetAllocations, 'nominal'));
        $remainingIncome = $totalIncome - $totalBudget;
        Session::put(['budgetAllocations' => $budgetAllocations, 'totalBudget' => $totalBudget, 'totalIncome' => $totalIncome, 'remainingIncome' => $remainingIncome]);
        return view('kalkulator.result', compact('totalIncome', 'budgetAllocations', 'totalBudget', 'remainingIncome'));
    }

    public function showResult(Request $request)
    {
        $totalIncome = Session::get('totalIncome', (float)$request->input('monthly_income') + (float)$request->input('additional_income'));
        $budgetAllocations = Session::get('budgetAllocations', []);
        $totalBudget = Session::get('totalBudget', array_sum(array_column($budgetAllocations, 'nominal')));
        $remainingIncome = $totalIncome - $totalBudget;
        return view('kalkulator.result', compact('totalIncome', 'budgetAllocations', 'totalBudget', 'remainingIncome'));
    }

    public function cetak_pdf(Request $request)
    {
        $data = ['totalIncome' => Session::get('totalIncome'), 'budgetAllocations' => Session::get('budgetAllocations'), 'totalBudget' => Session::get('totalBudget'), 'remainingIncome' => Session::get('remainingIncome')];
        return PDF::loadview('Kalkulator.pdf', $data)->stream('');
    }

    public function destroy($hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        if (HasilProsesAnggaran::where('id_proses_anggaran', $id)->delete()) return response()->json(['message' => 'Data berhasil dihapus']);
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) return response()->json(['message' => 'No data selected'], 400);
        $decodedIds = array_filter(array_map(fn($hash) => Hashids::decode($hash)[0] ?? null, $ids));
        if (empty($decodedIds)) return response()->json(['message' => 'Invalid data'], 400);
        $count = HasilProsesAnggaran::whereIn('id_proses_anggaran', $decodedIds)->where('id_user', Auth::id())->delete();
        return $count > 0 ? response()->json(['message' => "$count data berhasil dihapus"]) : response()->json(['message' => 'Gagal menghapus data atau data tidak ditemukan'], 404);
    }

    public function show(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        $HasilProsesAnggaran = HasilProsesAnggaran::with('user')->findOrFail($id);
        $idPengeluaranList = $HasilProsesAnggaran->jenis_pengeluaran ?? [];

        if (!is_array($idPengeluaranList)) {
            if (is_string($idPengeluaranList)) {
                $decoded = json_decode($idPengeluaranList, true);
                $idPengeluaranList = is_array($decoded) ? $decoded : [$idPengeluaranList];
            } else {
                $idPengeluaranList = [$idPengeluaranList];
            }
        }

        $allTransactions = Transaksi::with('pengeluaranRelation')
            ->where('id_user', $HasilProsesAnggaran->id_user)
            ->whereBetween('tgl_transaksi', [$HasilProsesAnggaran->tanggal_mulai, $HasilProsesAnggaran->tanggal_selesai])
            ->get();

        $filteredTransactions = $allTransactions->filter(function ($t) use ($idPengeluaranList) {
            return in_array((string)$t->pengeluaran, array_map('strval', $idPengeluaranList));
        });

        // Search in details
        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower($request->search);
            $filteredTransactions = $filteredTransactions->filter(function ($t) use ($search) {
                $categoryName = strtolower($t->pengeluaranRelation->nama ?? '');
                $keterangan = strtolower($t->keterangan ?? '');
                return str_contains($categoryName, $search) || str_contains($keterangan, $search);
            });
        }

        // Sort in details
        $sort = $request->get('sort', 'tgl_transaksi');
        $direction = $request->get('direction', 'asc');

        if ($direction === 'asc') {
            $filteredTransactions = $filteredTransactions->sortBy($sort);
        } else {
            $filteredTransactions = $filteredTransactions->sortByDesc($sort);
        }

        // Manual Pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        $items = $filteredTransactions->forPage($page, $perPage)->values();
        $transaksi = new \Illuminate\Pagination\LengthAwarePaginator($items, $filteredTransactions->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {

            return view('kalkulator._transaction_table', compact('transaksi', 'HasilProsesAnggaran'))->render();
        }

        $namaPengeluaran = Pengeluaran::whereIn('id', $idPengeluaranList)->pluck('nama')->toArray();
        $total = count($namaPengeluaran);

        // ─── Burn Rate Calculation ───────────────────────────────────────────
        $burnRate = null;
        $startDate  = \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_mulai)->startOfDay();
        $endDate    = \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_selesai)->endOfDay();
        $today      = \Carbon\Carbon::now()->startOfDay();

        // Only compute when the period has started
        if ($today->gte($startDate)) {
            $totalDays     = (int) $startDate->diffInDays($endDate) + 1;          // total days in period
            $daysElapsed   = (int) $startDate->diffInDays($today->min($endDate)) + 1; // days passed (capped at end)
            $daysRemaining = max(0, (int) $today->diffInDays($endDate));           // days left from today

            $totalSpent    = (float) $HasilProsesAnggaran->anggaran_yang_digunakan;
            $totalBudget   = (float) $HasilProsesAnggaran->nominal_anggaran;

            if ($daysElapsed > 0 && $totalBudget > 0) {
                $dailyRate           = $totalSpent / $daysElapsed;                 // avg spend per day
                $projectedTotal      = $dailyRate * $totalDays;                    // projected total if pace continues
                $projectedRemaining  = $dailyRate * $daysRemaining;               // projected spend in remaining days
                $spentPercentage     = ($totalSpent / $totalBudget) * 100;
                $idealPercentage     = ($daysElapsed / $totalDays) * 100;         // ideal % spent by now
                $daysUntilBudgetOut  = $dailyRate > 0 ? (int) (($totalBudget - $totalSpent) / $dailyRate) : null;

                $isOverBurning       = $projectedTotal > $totalBudget;            // will exceed budget
                $isBehindPace        = $spentPercentage > ($idealPercentage * 1.2); // spending 20% faster than ideal

                $burnRate = [
                    'total_days'           => $totalDays,
                    'days_elapsed'         => $daysElapsed,
                    'days_remaining'       => $daysRemaining,
                    'total_spent'          => $totalSpent,
                    'total_budget'         => $totalBudget,
                    'daily_rate'           => $dailyRate,
                    'projected_total'      => $projectedTotal,
                    'projected_remaining'  => $projectedRemaining,
                    'spent_percentage'     => $spentPercentage,
                    'ideal_percentage'     => $idealPercentage,
                    'days_until_out'       => $daysUntilBudgetOut,
                    'is_over_burning'      => $isOverBurning,
                    'is_behind_pace'       => $isBehindPace,
                    'alert_triggered'      => $isOverBurning || $isBehindPace,
                ];
            }
        }
        // ────────────────────────────────────────────────────────────────────

        return view('kalkulator.show', compact('HasilProsesAnggaran', 'total', 'namaPengeluaran', 'transaksi', 'burnRate'));
    }
}
