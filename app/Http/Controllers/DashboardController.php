<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HasilProsesAnggaran;
use App\Services\DashboardDataService;
use App\ViewModels\DashboardViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Pinjaman;
use App\Models\Aset;
use App\Models\DanaDarurat;
use App\Models\BayarPinjaman;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // -------------------------------------------------------------------------
    // Helper: masking nominal
    // -------------------------------------------------------------------------
    protected function maskNominal(float $value, bool $show): string
    {
        return $show ? 'Rp ' . number_format($value, 0, ',', '.') : 'Rp ********';
    }

    // -------------------------------------------------------------------------
    // Helper: label status rasio
    // -------------------------------------------------------------------------
    private function ratioStatus(float $value, string $type): array
    {
        return match ($type) {
            'expense'   => $value < 70  ? ['label' => 'Sehat',       'class' => 'success']
                : ($value <= 90 ? ['label' => 'Waspada',      'class' => 'warning']
                    : ['label' => 'Bahaya',       'class' => 'danger']),

            'saving'    => $value > 20  ? ['label' => 'Sangat Sehat', 'class' => 'success']
                : ($value >= 10 ? ['label' => 'Sehat',        'class' => 'primary']
                    : ($value >= 0  ? ['label' => 'Waspada',      'class' => 'warning']
                        : ['label' => 'Defisit',      'class' => 'danger'])),

            'emergency' => $value >= 6  ? ['label' => 'Aman',        'class' => 'success']
                : ($value >= 3  ? ['label' => 'Cukup',        'class' => 'warning']
                    : ['label' => 'Bahaya',       'class' => 'danger']),
        };
    }

    // -------------------------------------------------------------------------
    // MAIN DASHBOARD
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $userId  = Auth::id();
        $uiStyle = 'corporate';
        $periode = $this->sanitizePeriode($request->integer('periode', 6));
        $bulan   = $request->integer('bulan', now()->month);
        $tahun   = $request->integer('tahun', now()->year);

        $service = new DashboardDataService($userId);

        // 1. Ambil data raw dari service
        $numbers  = $service->getDashboardNumbers();
        $wealth   = $service->getWealthData();
        $cashflow = $service->getCashflow($periode);
        $kategori = $service->getPengeluaranKategori($bulan, $tahun);
        $today    = $service->getTransaksiHariIni();
        $rasio    = $service->getRasioData($cashflow);

        $targetDanaDarurat = $service->getTargetDanaDarurat();

        // 2. Buat ViewModel — semua kalkulasi di sini
        $viewModel = new DashboardViewModel(
            totalAset: $wealth['totalAset'],
            totalDanaDarurat: $wealth['totalDanaDarurat'],
            totalSaldoDompet: $wealth['totalSaldoDompet'],
            totalHutang: $wealth['totalHutang'],
            targetDanaDarurat: $targetDanaDarurat,
            pemasukan: $numbers['pemasukan'],
            pengeluaran: $numbers['pengeluaran'],
            pemasukanLalu: $numbers['pemasukan_lalu'],
            pengeluaranLalu: $numbers['pengeluaran_lalu'],
            saldo: $numbers['saldo'],
            saldoLalu: $numbers['saldo_lalu'],
        );

        // 3. Tampilan nominal (show/hide)
        $showNominal = session('show_nominal', false);
        session(['dashboard_numbers' => $numbers]); // untuk toggleNominalAjax

        $saldoView          = $this->maskNominal($numbers['saldo'],          $showNominal);
        $pemasukanView      = $this->maskNominal($numbers['pemasukan'],       $showNominal);
        $pengeluaranView    = $this->maskNominal($numbers['pengeluaran'],     $showNominal);
        $pengeluaranHariIni = $this->maskNominal($numbers['hari_ini'],        $showNominal);
        $cicilanBesokView   = $this->maskNominal($numbers['cicilan_besok'],   $showNominal);

        // 4. Rasio status label
        $expenseStatus   = $this->ratioStatus($rasio['expenseRatio'],    'expense');
        $emergencyStatus = $this->ratioStatus($rasio['danaDaruratBulan'], 'emergency');

        // 5. Filter options anggaran
        $filterOptions = HasilProsesAnggaran::where('id_user', $userId)
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->orderBy('tanggal_mulai')
            ->get()
            ->unique(fn($row) => $row->tanggal_mulai . '_' . $row->tanggal_selesai)
            ->values();

        // 6. Kirim ke view
        return view('dashboard.index', array_merge(
            $viewModel->toArray(),
            [
                // Cashflow
                'cashflow'  => $cashflow,
                'periode'   => $periode,

                // Kategori pengeluaran
                'pengeluaranKategori'   => $kategori['pengeluaranKategori'],
                'totalPengeluaranBulan' => $kategori['totalPengeluaranBulan'],
                'bulan'                 => $bulan,
                'tahun'                 => $tahun,

                // Transaksi hari ini
                'transaksiHariIni'   => $today['transaksiHariIni'],
                'totalMasukHariIni'  => $today['totalMasukHariIni'],
                'totalKeluarHariIni' => $today['totalKeluarHariIni'],

                // Nominal views
                'saldoView'          => $saldoView,
                'pemasukanView'      => $pemasukanView,
                'pengeluaranView'    => $pengeluaranView,
                'pengeluaranHariIni' => $pengeluaranHariIni,
                'cicilanBesokView'   => $cicilanBesokView,
                'showNominal'        => $showNominal,

                // Raw numbers (untuk data-attribute JS)
                'numbers'           => $numbers,
                'pemasukanLalu'     => $numbers['pemasukan_lalu'],
                'pengeluaranLalu'   => $numbers['pengeluaran_lalu'],
                'saldoLalu'         => $numbers['saldo_lalu'],

                // Rasio
                'expenseRatio'                 => $rasio['expenseRatio'],
                'danaDaruratBulan'             => $rasio['danaDaruratBulan'],
                'rasio'                        => $rasio['rasio'],
                'rasio_inflasi'                => $rasio['rasio_inflasi'],
                'rasio_pengeluaran_pendapatan' => $rasio['rasio_pengeluaran_pendapatan'],
                'totalAsetPhysical'            => $rasio['totalAsetPhysical'],
                'totalPinjaman'                => $rasio['totalPinjaman'],
                'expenseStatus'                => $expenseStatus,
                'emergencyStatus'              => $emergencyStatus,

                // Misc
                'uiStyle'              => $uiStyle,
                'filterOptions'        => $filterOptions,
                'totalNominalToday'    => $today['totalKeluarHariIni'],
                'totalNominalMonthExp' => $numbers['pengeluaran'],
                'totalNominalMonthInc' => $numbers['pemasukan'],
            ]
        ));
    }

    // -------------------------------------------------------------------------
    // Toggle show/hide nominal (AJAX)
    // -------------------------------------------------------------------------
    public function toggleNominalAjax()
    {
        $show    = session('show_nominal', false);
        $newShow = !$show;
        session(['show_nominal' => $newShow]);

        $numbers = session('dashboard_numbers');
        if (!$numbers) {
            return response()->json(['error' => 'Dashboard numbers not found'], 422);
        }

        return response()->json([
            'show' => $newShow,
            'data' => [
                'saldo'        => $this->maskNominal($numbers['saldo'],        $newShow),
                'pemasukan'    => $this->maskNominal($numbers['pemasukan'],    $newShow),
                'pengeluaran'  => $this->maskNominal($numbers['pengeluaran'],  $newShow),
                'hari_ini'     => $this->maskNominal($numbers['hari_ini'],     $newShow),
                'cicilan_besok' => $this->maskNominal($numbers['cicilan_besok'] ?? 0, $newShow),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Filter AJAX (cashflow periode & expense bar bulan/tahun)
    // -------------------------------------------------------------------------
    public function filter(Request $request)
    {
        $userId  = Auth::id();
        $uiStyle = 'corporate';
        $service = new DashboardDataService($userId);

        if ($request->has('periode')) {
            $periode  = $this->sanitizePeriode($request->integer('periode'));
            $cashflow = $service->getCashflow($periode);

            return response()->json([
                'cashflow'  => view('dashboard.partials.cashflow-table', compact('cashflow', 'uiStyle'))->render(),
                'chartData' => [
                    'cashflow' => $cashflow->map(fn($row) => [
                        'bulan'             => Carbon::parse($row->bulan . '-01')->translatedFormat('F Y'),
                        'total_pemasukan'   => (float) $row->total_pemasukan,
                        'total_pengeluaran' => (float) $row->total_pengeluaran,
                    ]),
                ],
            ]);
        }

        if ($request->has(['bulan', 'tahun'])) {
            $bulan  = $request->integer('bulan');
            $tahun  = $request->integer('tahun');
            $result = $service->getPengeluaranKategori($bulan, $tahun);

            $pengeluaranKategori   = $result['pengeluaranKategori'];
            $totalPengeluaranBulan = $result['totalPengeluaranBulan'];

            return response()->json([
                'expenseBar'       => view('dashboard.partials.expense-bar-table', compact('pengeluaranKategori', 'totalPengeluaranBulan', 'uiStyle'))->render(),
                'totalPengeluaran' => number_format((float) $totalPengeluaranBulan, 0, ',', '.'),
            ]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    // -------------------------------------------------------------------------
    // Net Worth Detail Page
    // -------------------------------------------------------------------------
    public function netWorth(Request $request)
    {
        $userId  = Auth::id();
        $uiStyle = 'corporate';
        $periode = $this->sanitizePeriode($request->integer('periode', 6));

        return view('dashboard.net-worth', compact('uiStyle', 'periode'));
    }

    // -------------------------------------------------------------------------
    // Net Worth History (AJAX)
    // -------------------------------------------------------------------------
    public function getNetWorthHistory(Request $request)
    {
        $userId  = Auth::id();
        $periode = $this->sanitizePeriode($request->integer('periode', 6));

        $netWorthHistory = [];
        $startPeriod     = now()->subMonths($periode - 1)->startOfMonth();
        $endPeriod       = now()->endOfMonth();

        // Pre-fetch untuk hindari N+1
        $allAssets   = Aset::where('id_user', $userId)->get();
        $allDD       = DanaDarurat::where('id_user', $userId)->get();
        $allPinjaman = Pinjaman::where('id_user', $userId)->get();
        $allBayar    = BayarPinjaman::where('id_user', $userId)->get();
        $allWallets  = \App\Models\Dompet::where('id_user', $userId)->get();
        $allTrx      = Transaksi::where('id_user', $userId)->whereNotNull('dompet_id')->get();

        $currentWalletTotal = $allWallets->sum(fn($w) => (float)$w->saldo);

        $currentMonth = $startPeriod->copy();

        while ($currentMonth <= $endPeriod) {
            $monthEnd = $currentMonth->copy()->endOfMonth();
            $monthStr = $currentMonth->translatedFormat('M Y');

            // Aset
            $assetsFiltered = $allAssets->filter(function ($aset) use ($monthEnd) {
                $purchased     = Carbon::parse($aset->tanggal_pembelian) <= $monthEnd;
                $notDisposed   = !$aset->is_disposed || ($aset->tanggal_disposal && Carbon::parse($aset->tanggal_disposal) > $monthEnd);
                return $purchased && $notDisposed;
            });
            $assetsSum = $assetsFiltered->sum('harga_beli');

            // Dana Darurat
            $ddFiltered = $allDD->filter(fn($dd) => Carbon::parse($dd->tgl_transaksi_dana_darurat) <= $monthEnd);
            $ddSum = $ddFiltered->reduce(fn($carry, $item) => $item->jenis_transaksi_dana_darurat == 1
                ? $carry + (float) $item->nominal_dana_darurat
                : $carry - (float) $item->nominal_dana_darurat, 0);

            // Hutang
            $hutangTotal = $allPinjaman->filter(fn($p) => Carbon::parse($p->start_date) <= $monthEnd)->sum('jumlah_pinjaman');
            $bayarTotal  = $allBayar->filter(fn($b) => Carbon::parse($b->tgl_bayar) <= $monthEnd)->sum('jumlah_bayar');
            $totalHutang = $hutangTotal - $bayarTotal;

            // Dompet (Wallet) - Reconstruct historical balance
            $walletSum = 0;
            foreach ($allWallets as $wallet) {
                if ($wallet->created_at <= $monthEnd) {
                    $trxAfter = $allTrx->filter(fn($t) => $t->dompet_id == $wallet->id && Carbon::parse($t->tgl_transaksi) > $monthEnd);
                    $pemasukanAfter = $trxAfter->sum(fn($t) => (float)$t->nominal_pemasukan);
                    $pengeluaranAfter = $trxAfter->sum(fn($t) => (float)$t->nominal);
                    $walletSum += ((float)$wallet->saldo - $pemasukanAfter + $pengeluaranAfter);
                }
            }

            $totalKekayaan = $assetsSum + $ddSum + $walletSum;

            $netWorthHistory[] = [
                'bulan'        => $monthStr,
                'total_aset'   => (float) $totalKekayaan,
                'total_hutang' => (float) $totalHutang,
                'net_worth'    => (float) ($totalKekayaan - $totalHutang),
                'details'      => [
                    'assets'    => $assetsFiltered->map(fn($a) => [
                        'name'  => $a->nama_aset,
                        'value' => (float) $a->harga_beli,
                        'date'  => Carbon::parse($a->tanggal_pembelian)->translatedFormat('d M Y'),
                    ])->values(),
                    'emergency' => $ddFiltered->map(fn($d) => [
                        'name'  => $d->keterangan ?? ($d->jenis_transaksi_dana_darurat == 1 ? 'Top Up' : 'Withdrawal'),
                        'value' => (float) $d->nominal_dana_darurat * ($d->jenis_transaksi_dana_darurat == 1 ? 1 : -1),
                        'date'  => Carbon::parse($d->tgl_transaksi_dana_darurat)->translatedFormat('d M Y'),
                    ])->values(),
                    'wallets'   => $allWallets->filter(fn($w) => $w->created_at <= $monthEnd)
                        ->map(function ($w) use ($allTrx, $monthEnd) {
                             $trxAfter = $allTrx->filter(fn($t) => $t->dompet_id == $w->id && Carbon::parse($t->tgl_transaksi) > $monthEnd);
                             $pemasukanAfter = $trxAfter->sum(fn($t) => (float)$t->nominal_pemasukan);
                             $pengeluaranAfter = $trxAfter->sum(fn($t) => (float)$t->nominal);
                             return ['name' => $w->nama, 'value' => ((float)$w->saldo - $pemasukanAfter + $pengeluaranAfter), 'date' => $w->created_at->translatedFormat('d M Y')];
                        })->filter(fn($v) => $v['value'] > 0)->values(),
                    'loans'     => $allPinjaman->filter(fn($p) => Carbon::parse($p->start_date) <= $monthEnd)
                        ->map(function ($p) use ($allBayar, $monthEnd) {
                            $lunas = $allBayar->filter(fn($b) => $b->id_pinjaman == $p->id_pinjaman && Carbon::parse($b->tgl_bayar) <= $monthEnd)->sum('jumlah_bayar');
                            return ['name' => $p->nama_pinjaman, 'value' => (float) ($p->jumlah_pinjaman - $lunas), 'date' => Carbon::parse($p->start_date)->translatedFormat('d M Y')];
                        })->filter(fn($l) => $l['value'] > 0)->values(),
                ],
            ];

            $currentMonth->addMonth();
        }

        return response()->json($netWorthHistory);
    }

    // -------------------------------------------------------------------------
    // Chart endpoints (legacy — bisa dihapus jika sudah tidak dipakai)
    // -------------------------------------------------------------------------
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
            $data[$key]['pemasukan']   = ($data[$key]['pemasukan']   ?? 0) + (float) $t->nominal_pemasukan;
        }

        return $keysOnly
            ? response()->json(['labels' => array_keys($data), 'data_pengeluaran' => array_column($data, 'pengeluaran'), 'data_pemasukan' => array_column($data, 'pemasukan')])
            : response()->json(['labels' => array_keys($data), 'pengeluaran' => array_column($data, 'pengeluaran'), 'pemasukan' => array_column($data, 'pemasukan')]);
    }

    public function getPieData()
    {
        $now      = Carbon::now();
        $transaksi = Transaksi::where('id_user', Auth::id())->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)
            ->get()->filter(fn($t) => !empty($t->pengeluaran));

        $labels = [];
        $data   = [];

        foreach ($transaksi as $t) {
            $index = array_search($t->pengeluaran, $labels);
            if ($index !== false) {
                $data[$index] += (float) $t->nominal;
            } else {
                $labels[] = $t->pengeluaran;
                $data[]   = (float) $t->nominal;
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
        $month       = $request->query('month');
        $year        = $request->query('year');

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
                'tgl_transaksi'   => $t->tgl_transaksi,
                'keterangan'      => $t->keterangan,
                'nominal'         => (float) $t->nominal,
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

        $anggarans = $query->get();

        return response()->json([
            'labels'   => $anggarans->pluck('nama_anggaran')->values(),
            'ids'      => $anggarans->pluck('id_proses_anggaran'),
            'datasets' => [
                ['name' => 'Anggaran',  'data' => $anggarans->pluck('nominal_anggaran')->map(fn($v) => (float) $v)->values()],
                ['name' => 'Realisasi', 'data' => $anggarans->pluck('anggaran_yang_digunakan')->map(fn($v) => (float) $v)->values()],
                ['name' => 'Sisa',      'data' => $anggarans->pluck('sisa_anggaran')->map(fn($v) => (float) $v)->values()],
            ],
            'table' => $anggarans,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/bimmo')->with('success', 'Successful Log out');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------
    private function sanitizePeriode(int $value): int
    {
        return in_array($value, [2, 6, 12]) ? $value : 6;
    }
}
