<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\Aset;
use App\Models\BayarPinjaman;
use App\Models\DanaDarurat;
use App\Models\Dompet;
use App\Models\HasilProsesAnggaran;
use App\Models\Pemasukan;
use App\Models\Pinjaman;
use App\Models\Transaksi;
use App\Services\DashboardDataService;
use App\ViewModels\DashboardViewModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected function maskNominal(float $value, bool $show): string
    {
        return $show ? 'Rp ' . number_format($value, 0, ',', '.') : 'Rp ********';
    }

    private function ratioStatus(float $value, string $type): array
    {
        return match ($type) {
            'expense' => $value < 70
                ? ['label' => 'Sehat', 'class' => 'success']
                : ($value <= 90
                    ? ['label' => 'Waspada', 'class' => 'warning']
                    : ['label' => 'Bahaya', 'class' => 'danger']),

            'saving' => $value > 20
                ? ['label' => 'Sangat Sehat', 'class' => 'success']
                : ($value >= 10
                    ? ['label' => 'Sehat', 'class' => 'primary']
                    : ($value >= 0
                        ? ['label' => 'Waspada', 'class' => 'warning']
                        : ['label' => 'Defisit', 'class' => 'danger'])),

            'emergency' => $value >= 6
                ? ['label' => 'Aman', 'class' => 'success']
                : ($value >= 3
                    ? ['label' => 'Cukup', 'class' => 'warning']
                    : ['label' => 'Bahaya', 'class' => 'danger']),

            'debt' => $value <= 30
                ? ['label' => 'Aman', 'class' => 'success']
                : ($value <= 35
                    ? ['label' => 'Waspada', 'class' => 'warning']
                    : ['label' => 'Bahaya', 'class' => 'danger']),
        };
    }

    public function index(Request $request)
    {
        return view('dashboard.index', $this->buildDashboardViewData($request));
    }

    public function toggleNominalAjax()
    {
        $show = session('show_nominal', false);
        $newShow = !$show;
        session(['show_nominal' => $newShow]);

        $numbers = session('dashboard_numbers');
        if (!$numbers) {
            return response()->json(['error' => 'Dashboard numbers not found'], 422);
        }

        $goals = session('dashboard_goals');
        if (!$goals) {
            $service = new DashboardDataService(Auth::id());
            $goals = $service->getFinancialGoalsSummary();
        }

        return response()->json([
            'show' => $newShow,
            'data' => [
                'saldo' => $this->maskNominal($numbers['saldo'], $newShow),
                'pemasukan' => $this->maskNominal($numbers['pemasukan'], $newShow),
                'pengeluaran' => $this->maskNominal($numbers['pengeluaran'], $newShow),
                'hari_ini' => $this->maskNominal($numbers['hari_ini'], $newShow),
                'cicilan_besok' => $this->maskNominal($numbers['cicilan_besok'] ?? 0, $newShow),
                'financial_goals_collected' => $this->maskNominal((float) ($goals['totalCollectedActive'] ?? 0), $newShow),
                'financial_goals_target' => $this->maskNominal((float) ($goals['totalTargetActive'] ?? 0), $newShow),
            ],
        ]);
    }

    public function filter(Request $request)
    {
        $userId = Auth::id();
        $uiStyle = 'corporate';
        $service = new DashboardDataService($userId);

        if ($request->has('periode')) {
            $periode = $this->sanitizePeriode($request->integer('periode'));
            $cashflow = $service->getCashflow($periode);

            return response()->json([
                'cashflow' => view('dashboard.partials.cashflow-table', compact('cashflow', 'uiStyle'))->render(),
                'chartData' => [
                    'cashflow' => $cashflow->map(fn($row) => [
                        'bulan' => Carbon::parse($row->bulan . '-01')->translatedFormat('F Y'),
                        'total_pemasukan' => (float) $row->total_pemasukan,
                        'total_pengeluaran' => (float) $row->total_pengeluaran,
                    ]),
                ],
            ]);
        }

        if ($request->has(['bulan', 'tahun'])) {
            $bulan = $request->integer('bulan');
            $tahun = $request->integer('tahun');
            $result = $service->getPengeluaranKategori($bulan, $tahun);

            $pengeluaranKategori = $result['pengeluaranKategori'];
            $totalPengeluaranBulan = $result['totalPengeluaranBulan'];

            return response()->json([
                'expenseBar' => view('dashboard.partials.expense-bar-table', compact('pengeluaranKategori', 'totalPengeluaranBulan', 'uiStyle'))->render(),
                'totalPengeluaran' => number_format((float) $totalPengeluaranBulan, 0, ',', '.'),
            ]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function netWorth(Request $request)
    {
        return view('dashboard.net-worth', [
            'uiStyle' => 'corporate',
            'periode' => $this->sanitizePeriode($request->integer('periode', 6)),
        ]);
    }

    public function getNetWorthHistory(Request $request)
    {
        return response()->json(
            $this->buildNetWorthHistory(
                Auth::id(),
                $this->sanitizePeriode($request->integer('periode', 6))
            )
        );
    }

    public function lineData()
    {
        return $this->buildChartResponse();
    }

    public function getChartData()
    {
        return $this->buildChartResponse(keysOnly: true);
    }

    private function buildChartResponse(bool $keysOnly = false)
    {
        $transaksi = Transaksi::where('id_user', Auth::id())->where('status', '1')->get();

        $data = [];
        foreach ($transaksi as $t) {
            $key = date('F Y', strtotime($t->tgl_transaksi));
            $data[$key]['pengeluaran'] = ($data[$key]['pengeluaran'] ?? 0) + (float) $t->nominal;
            $data[$key]['pemasukan'] = ($data[$key]['pemasukan'] ?? 0) + (float) $t->nominal_pemasukan;
        }

        return $keysOnly
            ? response()->json([
                'labels' => array_keys($data),
                'data_pengeluaran' => array_column($data, 'pengeluaran'),
                'data_pemasukan' => array_column($data, 'pemasukan'),
            ])
            : response()->json([
                'labels' => array_keys($data),
                'pengeluaran' => array_column($data, 'pengeluaran'),
                'pemasukan' => array_column($data, 'pemasukan'),
            ]);
    }

    public function getPieData()
    {
        $now = Carbon::now();
        $transaksi = Transaksi::where('id_user', Auth::id())
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->get()
            ->filter(fn($t) => !empty($t->pengeluaran));

        $labels = [];
        $data = [];

        foreach ($transaksi as $t) {
            $index = array_search($t->pengeluaran, $labels);
            if ($index !== false) {
                $data[$index] += (float) $t->nominal;
            } else {
                $labels[] = $t->pengeluaran;
                $data[] = (float) $t->nominal;
            }
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function TodayTransactions()
    {
        $now = Carbon::now('Asia/Jakarta');

        return response()->json(
            Transaksi::with(['pengeluaranRelation', 'pemasukanRelation'])
                ->where('id_user', Auth::id())
                ->whereBetween('tgl_transaksi', [$now->startOfDay(), $now->copy()->endOfDay()])
                ->orderBy('tgl_transaksi', 'desc')
                ->get()
        );
    }

    public function getTransaksiByPengeluaran(Request $request)
    {
        $pengeluaran = $request->query('pengeluaran');
        $month = $request->query('month');
        $year = $request->query('year');

        if (!$pengeluaran || !$month || !$year) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 400);
        }

        $data = Transaksi::with('pengeluaranRelation')
            ->where('id_user', Auth::id())
            ->whereMonth('tgl_transaksi', $month)
            ->whereYear('tgl_transaksi', $year)
            ->get()
            ->filter(fn($t) => $t->pengeluaran == $pengeluaran)
            ->map(fn($t) => (object) [
                'tgl_transaksi' => $t->tgl_transaksi,
                'keterangan' => $t->keterangan,
                'nominal' => (float) $t->nominal,
                'pengeluaran_nama' => $t->pengeluaranRelation->nama ?? 'Unknown',
            ])->values();

        return response()->json($data);
    }

    public function AnggaranChart(Request $request)
    {
        $query = HasilProsesAnggaran::where('id_user', Auth::id());

        if ($request->filter) {
            [$mulai, $selesai] = explode('_', $request->filter);
            $query->where('tanggal_mulai', $mulai)->where('tanggal_selesai', $selesai);
        }

        $anggarans = $query->get()->map(function ($a) {
            $a->burn_rate = $a->calculateBurnRate();

            $categories = [];
            $jenisPengeluaran = $a->jenis_pengeluaran;
            if (!is_array($jenisPengeluaran)) {
                $jenisPengeluaran = is_string($jenisPengeluaran) ? json_decode($jenisPengeluaran, true) : [$jenisPengeluaran];
            }

            if (is_array($jenisPengeluaran) && !empty($jenisPengeluaran)) {
                $totalNominal = (float) $a->nominal_anggaran;
                $allTrxInRange = Transaksi::where('id_user', Auth::id())
                    ->whereBetween('tgl_transaksi', [$a->tanggal_mulai, $a->tanggal_selesai])
                    ->get();
                $categoryNames = \App\Models\Pengeluaran::whereIn('id', $jenisPengeluaran)->pluck('nama', 'id');

                foreach ($jenisPengeluaran as $id) {
                    $used = (float) $allTrxInRange->filter(fn($t) => (string) $t->pengeluaran === (string) $id)->sum('nominal');
                    if ($used > 0) {
                        $categories[] = [
                            'id' => $id,
                            'nama' => $categoryNames[$id] ?? 'Unknown',
                            'nominal' => $used,
                            'persentase' => $totalNominal > 0 ? ($used / $totalNominal) * 100 : 0,
                        ];
                    }
                }

                usort($categories, fn($a, $b) => $b['nominal'] <=> $a['nominal']);
                $categories = array_slice($categories, 0, 1);
            }

            $a->kategori_breakdown = $categories;
            return $a;
        });

        return response()->json([
            'labels' => $anggarans->pluck('nama_anggaran')->values(),
            'ids' => $anggarans->pluck('id_proses_anggaran'),
            'datasets' => [
                ['name' => 'Anggaran', 'data' => $anggarans->pluck('nominal_anggaran')->map(fn($v) => (float) $v)->values()],
                ['name' => 'Realisasi', 'data' => $anggarans->pluck('anggaran_yang_digunakan')->map(fn($v) => (float) $v)->values()],
                ['name' => 'Sisa', 'data' => $anggarans->pluck('sisa_anggaran')->map(fn($v) => (float) $v)->values()],
            ],
            'table' => $anggarans,
        ]);
    }

    public function syncAnggaran()
    {
        $userId = Auth::id();
        $records = HasilProsesAnggaran::where('id_user', $userId)->get();

        if ($records->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data anggaran untuk disinkronisasi'], 404);
        }

        foreach ($records as $prosesAnggaran) {
            $originalAnggaran = Anggaran::where('id_user', $userId)
                ->where('nama_anggaran', $prosesAnggaran->nama_anggaran)
                ->first();

            if ($originalAnggaran) {
                $prosesAnggaran->persentase_anggaran = $originalAnggaran->persentase_anggaran;
                $prosesAnggaran->jenis_pengeluaran = $originalAnggaran->id_pengeluaran;
            }

            $idPemasukans = $prosesAnggaran->jenis_pemasukan;
            if (empty($idPemasukans)) {
                $idPemasukans = Pemasukan::where('id_user', $userId)->pluck('id')->toArray();
            }

            if (!empty($idPemasukans)) {
                if (!is_array($idPemasukans)) {
                    $idPemasukans = json_decode((string) $idPemasukans, true) ?? [$idPemasukans];
                }

                $allIncomesInRange = Transaksi::where('id_user', $userId)
                    ->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])
                    ->get();

                $totalIncome = $allIncomesInRange
                    ->filter(function ($t) use ($idPemasukans) {
                        return in_array((string) $t->pemasukan, array_map('strval', $idPemasukans));
                    })
                    ->sum(function ($t) {
                        $val = (string) ($t->nominal_pemasukan ?? '0');
                        $cleanVal = str_replace(['.', ','], ['', '.'], $val);
                        return (float) $cleanVal;
                    });

                $prosesAnggaran->nominal_anggaran = ($prosesAnggaran->persentase_anggaran / 100) * $totalIncome;
            }

            $jenisPengeluaran = $prosesAnggaran->jenis_pengeluaran;
            if (!is_array($jenisPengeluaran)) {
                if (is_string($jenisPengeluaran)) {
                    $decoded = json_decode($jenisPengeluaran, true);
                    $jenisPengeluaran = is_array($decoded) ? $decoded : [$jenisPengeluaran];
                } else {
                    $jenisPengeluaran = [$jenisPengeluaran];
                }
            }

            $allTrxInRange = Transaksi::where('id_user', $userId)
                ->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])
                ->get();

            $totalTransaksi = $allTrxInRange
                ->filter(function ($t) use ($jenisPengeluaran) {
                    return in_array((string) $t->pengeluaran, array_map('strval', $jenisPengeluaran));
                })
                ->sum(fn($t) => (float) $t->nominal);

            $prosesAnggaran->anggaran_yang_digunakan = $totalTransaksi;
            $prosesAnggaran->save();
        }

        return response()->json(['message' => 'Sinkronisasi berhasil!']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/bimmo')->with('success', 'Successful Log out');
    }

    private function sanitizePeriode(int $value): int
    {
        return in_array($value, [2, 6, 12]) ? $value : 6;
    }

    private function buildDashboardViewData(Request $request): array
    {
        $userId = Auth::id();
        $uiStyle = 'corporate';
        $periode = $this->sanitizePeriode($request->integer('periode', 6));
        $bulan = $request->integer('bulan', now()->month);
        $tahun = $request->integer('tahun', now()->year);

        $service = new DashboardDataService($userId);
        $numbers = $service->getDashboardNumbers();
        $wealth = $service->getWealthData();
        $cashflow = $service->getCashflow($periode);
        $kategori = $service->getPengeluaranKategori($bulan, $tahun);
        $today = $service->getTransaksiHariIni();
        $rasio = $service->getRasioData($cashflow);
        $goals = $service->getFinancialGoalsSummary();

        $viewModel = new DashboardViewModel(
            totalAset: $wealth['totalAset'],
            totalDanaDarurat: $wealth['totalDanaDarurat'],
            totalSaldoDompet: $wealth['totalSaldoDompet'],
            totalHutang: $wealth['totalHutang'],
            targetDanaDarurat: $service->getTargetDanaDarurat(),
            pemasukan: $numbers['pemasukan'],
            pengeluaran: $numbers['pengeluaran'],
            pemasukanLalu: $numbers['pemasukan_lalu'],
            pengeluaranLalu: $numbers['pengeluaran_lalu'],
            saldo: $numbers['saldo'],
            saldoLalu: $numbers['saldo_lalu'],
        );

        $showNominal = session('show_nominal', false);
        session(['dashboard_numbers' => $numbers]);
        session(['dashboard_goals' => $goals]);

        return array_merge($viewModel->toArray(), [
            'cashflow' => $cashflow,
            'periode' => $periode,
            'pengeluaranKategori' => $kategori['pengeluaranKategori'],
            'totalPengeluaranBulan' => $kategori['totalPengeluaranBulan'],
            'bulan' => $bulan,
            'tahun' => $tahun,
            'transaksiHariIni' => $today['transaksiHariIni'],
            'totalMasukHariIni' => $today['totalMasukHariIni'],
            'totalKeluarHariIni' => $today['totalKeluarHariIni'],
            'saldoView' => $this->maskNominal($numbers['saldo'], $showNominal),
            'pemasukanView' => $this->maskNominal($numbers['pemasukan'], $showNominal),
            'pengeluaranView' => $this->maskNominal($numbers['pengeluaran'], $showNominal),
            'pengeluaranHariIni' => $this->maskNominal($numbers['hari_ini'], $showNominal),
            'cicilanBesokView' => $this->maskNominal($numbers['cicilan_besok'], $showNominal),
            'showNominal' => $showNominal,
            'numbers' => $numbers,
            'pemasukanLalu' => $numbers['pemasukan_lalu'],
            'pengeluaranLalu' => $numbers['pengeluaran_lalu'],
            'saldoLalu' => $numbers['saldo_lalu'],
            'expenseRatio' => $rasio['expenseRatio'],
            'danaDaruratBulan' => $rasio['danaDaruratBulan'],
            'rasio' => $rasio['rasio'],
            'rasio_inflasi' => $rasio['rasio_inflasi'],
            'rasio_pengeluaran_pendapatan' => $rasio['rasio_pengeluaran_pendapatan'],
            'totalAsetPhysical' => $rasio['totalAsetPhysical'],
            'totalPinjaman' => $rasio['totalPinjaman'],
            'totalCicilanMonth' => $rasio['totalCicilanMonth'],
            'debtServiceRatio' => $rasio['debtServiceRatio'],
            'expenseStatus' => $this->ratioStatus($rasio['expenseRatio'], 'expense'),
            'emergencyStatus' => $this->ratioStatus($rasio['danaDaruratBulan'], 'emergency'),
            'debtStatus' => $this->ratioStatus($rasio['debtServiceRatio'], 'debt'),
            'uiStyle' => $uiStyle,
            'filterOptions' => $this->getBudgetFilterOptions($userId),
            'totalNominalToday' => $today['totalKeluarHariIni'],
            'totalNominalMonthExp' => $numbers['pengeluaran'],
            'totalNominalMonthInc' => $numbers['pemasukan'],

            'financialGoalsActiveCount' => $goals['activeCount'],
            'financialGoalsOverallPercent' => $goals['overallPercent'],
            'financialGoalsNextDue' => $goals['nextDue'],
            'financialGoalsItems' => collect($goals['items'])->map(function ($item) use ($showNominal) {
                $item['collectedView'] = $this->maskNominal((float) ($item['collected'] ?? 0), $showNominal);
                $item['targetView'] = $this->maskNominal((float) ($item['target'] ?? 0), $showNominal);
                return $item;
            })->all(),
            'financialGoalsTotalCollectedView' => $this->maskNominal((float) ($goals['totalCollectedActive'] ?? 0), $showNominal),
            'financialGoalsTotalTargetView' => $this->maskNominal((float) ($goals['totalTargetActive'] ?? 0), $showNominal),
        ]);
    }

    private function getBudgetFilterOptions(int $userId)
    {
        return HasilProsesAnggaran::where('id_user', $userId)
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->orderBy('tanggal_mulai')
            ->get()
            ->unique(fn($row) => $row->tanggal_mulai . '_' . $row->tanggal_selesai)
            ->values();
    }

    private function buildNetWorthHistory(int $userId, int $periode): array
    {
        $history = [];
        $startPeriod = now()->subMonths($periode - 1)->startOfMonth();
        $endPeriod = now()->endOfMonth();
        $allAssets = Aset::where('id_user', $userId)->get();
        $allDanaDarurat = DanaDarurat::where('id_user', $userId)->get();
        $allPinjaman = Pinjaman::where('id_user', $userId)->get();
        $allBayar = BayarPinjaman::where('id_user', $userId)->get();
        $allWallets = Dompet::where('id_user', $userId)->get();
        $allWalletTransactions = Transaksi::where('id_user', $userId)->whereNotNull('dompet_id')->get();

        $currentMonth = $startPeriod->copy();

        while ($currentMonth <= $endPeriod) {
            $monthEnd = $currentMonth->copy()->endOfMonth();

            $assets = $allAssets
                ->filter(function ($aset) use ($monthEnd) {
                    $purchased = Carbon::parse($aset->tanggal_pembelian) <= $monthEnd;
                    $notDisposed = !$aset->is_disposed || ($aset->tanggal_disposal && Carbon::parse($aset->tanggal_disposal) > $monthEnd);

                    return $purchased && $notDisposed;
                })
                ->map(fn($aset) => [
                    'name' => $aset->nama_aset,
                    'value' => (float) $aset->harga_beli,
                    'date' => Carbon::parse($aset->tanggal_pembelian)->translatedFormat('d M Y'),
                ])
                ->values();

            $emergencyFunds = $allDanaDarurat
                ->filter(fn($item) => Carbon::parse($item->tgl_transaksi_dana_darurat) <= $monthEnd)
                ->map(fn($item) => [
                    'name' => $item->keterangan ?? ($item->jenis_transaksi_dana_darurat == 1 ? 'Top Up' : 'Withdrawal'),
                    'value' => (float) $item->nominal_dana_darurat * ($item->jenis_transaksi_dana_darurat == 1 ? 1 : -1),
                    'date' => Carbon::parse($item->tgl_transaksi_dana_darurat)->translatedFormat('d M Y'),
                ])
                ->values();

            $wallets = $allWallets
                ->filter(fn($wallet) => $wallet->created_at <= $monthEnd)
                ->map(function ($wallet) use ($allWalletTransactions, $monthEnd) {
                    $trxAfter = $allWalletTransactions
                        ->filter(fn($trx) => $trx->dompet_id == $wallet->id && Carbon::parse($trx->tgl_transaksi) > $monthEnd);
                    $pemasukanAfter = $trxAfter->sum(fn($trx) => (float) $trx->nominal_pemasukan);
                    $pengeluaranAfter = $trxAfter->sum(fn($trx) => (float) $trx->nominal);

                    return [
                        'name' => $wallet->nama,
                        'value' => (float) $wallet->saldo - $pemasukanAfter + $pengeluaranAfter,
                        'date' => $wallet->created_at->translatedFormat('d M Y'),
                    ];
                })
                ->filter(fn($wallet) => $wallet['value'] > 0)
                ->values();

            $loans = $allPinjaman
                ->filter(fn($pinjaman) => Carbon::parse($pinjaman->start_date) <= $monthEnd)
                ->map(function ($pinjaman) use ($allBayar, $monthEnd) {
                    $paymentsAfter = $allBayar
                        ->filter(fn($bayar) => $bayar->id_pinjaman == $pinjaman->id_pinjaman && Carbon::parse($bayar->tgl_bayar) > $monthEnd)
                        ->sum('jumlah_bayar');

                    return [
                        'name' => $pinjaman->nama_pinjaman,
                        'value' => (float) ($pinjaman->jumlah_pinjaman + $paymentsAfter),
                        'date' => Carbon::parse($pinjaman->start_date)->translatedFormat('d M Y'),
                    ];
                })
                ->filter(fn($loan) => $loan['value'] > 0)
                ->values();

            $totalAssets = collect($assets)->sum('value') + collect($emergencyFunds)->sum('value') + collect($wallets)->sum('value');
            $totalDebt = collect($loans)->sum('value');

            $history[] = [
                'bulan' => $currentMonth->translatedFormat('M Y'),
                'total_aset' => (float) $totalAssets,
                'total_hutang' => (float) $totalDebt,
                'net_worth' => (float) ($totalAssets - $totalDebt),
                'details' => [
                    'assets' => $assets,
                    'emergency' => $emergencyFunds,
                    'wallets' => $wallets,
                    'loans' => $loans,
                ],
            ];

            $currentMonth->addMonth();
        }

        return $history;
    }
}
