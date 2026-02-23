<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Pinjaman;
use App\Models\Barang;
use App\Models\DanaDarurat;
use App\Models\Anggaran;
use App\Models\HasilProsesAnggaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected function getDashboardNumbers()
    {
        $userId = Auth::id();
        $now = now();
        $lastMonth = now()->subMonth();

        // THIS MONTH
        $pemasukanBulanIni = Transaksi::where('id_user', $userId)
            ->whereMonth('tgl_transaksi', $now->month)
            ->whereYear('tgl_transaksi', $now->year)->get()->sum(fn($t) => (float)$t->nominal_pemasukan);

        $pengeluaranBulanIni = Transaksi::where('id_user', $userId)
            ->whereMonth('tgl_transaksi', $now->month)
            ->whereYear('tgl_transaksi', $now->year)->get()->sum(fn($t) => (float)$t->nominal);

        $pengeluaranHariIni = Transaksi::where('id_user', $userId)
            ->whereDate('tgl_transaksi', $now)->get()->sum(fn($t) => (float)$t->nominal);

        $saldo = $pemasukanBulanIni - $pengeluaranBulanIni;

        // LAST MONTH
        $pemasukanBulanLalu = Transaksi::where('id_user', $userId)
            ->whereMonth('tgl_transaksi', $lastMonth->month)
            ->whereYear('tgl_transaksi', $lastMonth->year)->get()->sum(fn($t) => (float)$t->nominal_pemasukan);

        $pengeluaranBulanLalu = Transaksi::where('id_user', $userId)
            ->whereMonth('tgl_transaksi', $lastMonth->month)
            ->whereYear('tgl_transaksi', $lastMonth->year)->get()->sum(fn($t) => (float)$t->nominal);

        $saldoBulanLalu = $pemasukanBulanLalu - $pengeluaranBulanLalu;

        // PERCENTAGE CALCULATIONS
        $persenPemasukan = $pemasukanBulanLalu > 0 ? (($pemasukanBulanIni - $pemasukanBulanLalu) / $pemasukanBulanLalu) * 100 : 0;
        $persenPengeluaran = $pengeluaranBulanLalu > 0 ? (($pengeluaranBulanIni - $pengeluaranBulanLalu) / $pengeluaranBulanLalu) * 100 : 0;
        $persenSaldo = $saldoBulanLalu != 0 ? (($saldo - $saldoBulanLalu) / abs($saldoBulanLalu)) * 100 : 0;

        return [
            'saldo' => $saldo,
            'pemasukan' => $pemasukanBulanIni,
            'pengeluaran' => $pengeluaranBulanIni,
            'hari_ini' => $pengeluaranHariIni,
            'persen_saldo' => round($persenSaldo, 1),
            'persen_pemasukan' => round($persenPemasukan, 1),
            'persen_pengeluaran' => round($persenPengeluaran, 1),
        ];
    }
    public function toggleNominalAjax()
    {
        $show = session('show_nominal', false);
        $newState = !$show;
        session(['show_nominal' => $newState]);

        $numbers = session('dashboard_numbers');
        if (!$numbers) return response()->json(['error' => 'Dashboard numbers not found'], 422);

        return response()->json([
            'show' => $newState,
            'data' => [
                'saldo' => $this->maskNominal($numbers['saldo'], $newState),
                'pemasukan' => $this->maskNominal($numbers['pemasukan'], $newState),
                'pengeluaran' => $this->maskNominal($numbers['pengeluaran'], $newState),
                'hari_ini' => $this->maskNominal($numbers['hari_ini'], $newState),
            ]
        ]);
    }

    protected function maskNominal($value, $show)
    {
        return $show ? 'Rp ' . number_format($value, 0, ',', '.') : 'Rp ********';
    }

    private function ratioStatus($value, $type)
    {
        return match ($type) {
            'expense' => $value < 70 ? ['label' => 'Sehat', 'class' => 'success'] : ($value <= 90 ? ['label' => 'Waspada', 'class' => 'warning'] : ['label' => 'Bahaya', 'class' => 'danger']),
            'saving' => $value > 20 ? ['label' => 'Sangat Sehat', 'class' => 'success'] : ($value >= 10 ? ['label' => 'Sehat', 'class' => 'primary'] : ($value >= 0 ? ['label' => 'Waspada', 'class' => 'warning'] : ['label' => 'Defisit', 'class' => 'danger'])),
            'emergency' => $value >= 6 ? ['label' => 'Aman', 'class' => 'success'] : ($value >= 3 ? ['label' => 'Cukup', 'class' => 'warning'] : ['label' => 'Bahaya', 'class' => 'danger']),
        };
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $periode = (int) request('periode', 6);
        if (!in_array($periode, [2, 6, 12])) $periode = 6;

        $cashflow = Transaksi::where('id_user', Auth::id())
            ->where('tgl_transaksi', '>=', now()->subMonths($periode - 1)->startOfMonth())
            ->get()
            ->groupBy(fn($t) => \Carbon\Carbon::parse($t->tgl_transaksi)->format('Y-m'))
            ->map(function ($items, $bulan) {
                return (object) [
                    'bulan' => $bulan,
                    'total_pemasukan' => $items->sum(fn($t) => (float)$t->nominal_pemasukan),
                    'total_pengeluaran' => $items->sum(fn($t) => (float)$t->nominal),
                    'selisih' => $items->sum(fn($t) => (float)$t->nominal_pemasukan) - $items->sum(fn($t) => (float)$t->nominal)
                ];
            })->sortBy('bulan')->values();

        $savingRateStatus = function ($rate) {
            if ($rate > 20) return ['label' => 'Sangat Sehat', 'class' => 'success'];
            if ($rate >= 10) return ['label' => 'Sehat', 'class' => 'primary'];
            if ($rate >= 0) return ['label' => 'Waspada', 'class' => 'warning'];
            return ['label' => 'Defisit', 'class' => 'danger'];
        };

        $savingRate = $cashflow->map(function ($row) use ($savingRateStatus) {
            $pendapatan = (float) $row->total_pemasukan;
            $pengeluaran = (float) $row->total_pengeluaran;
            $rate = $pendapatan > 0 ? round((($pendapatan - $pengeluaran) / $pendapatan) * 100, 2) : 0;
            $status = $savingRateStatus($rate);
            $row->saving_rate = $rate;
            $row->saving_label = $status['label'];
            $row->saving_class = $status['class'];
            return $row;
        });

        $totalPendapatan = $cashflow->sum('total_pemasukan');
        $totalPengeluaranAll = $cashflow->sum('total_pengeluaran');
        $expenseRatio = $totalPendapatan > 0 ? round(($totalPengeluaranAll / $totalPendapatan) * 100, 2) : 0;
        $latest = $cashflow->last();
        $savingRateLatest = $latest && $latest->total_pemasukan > 0 ? round((($latest->total_pemasukan - $latest->total_pengeluaran) / $latest->total_pemasukan) * 100, 2) : 0;

        $danaDarurat = \App\Models\DanaDarurat::where('id_user', Auth::id())->value('nominal_dana_darurat') ?? 0;
        $rataPengeluaran = $cashflow->count() > 0 ? $cashflow->avg('total_pengeluaran') : 0;
        $danaDaruratBulan = $rataPengeluaran > 0 ? round($danaDarurat / $rataPengeluaran, 1) : 0;

        $totalPinjaman = Pinjaman::where('id_user', $userId)->sum('jumlah_pinjaman');
        $totalBarang = Barang::where('id_user', $userId)->where('status', '1')->sum('harga');
        $rasio = $totalBarang > 0 ? ($totalPinjaman / $totalBarang) * 100 : 0;

        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $totalThisMonth = Transaksi::where('id_user', $userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float)$t->nominal);
        $totalLastMonth = Transaksi::where('id_user', $userId)->where('status', '1')->whereYear('tgl_transaksi', $lastMonth->year)->whereMonth('tgl_transaksi', $lastMonth->month)->get()->sum(fn($t) => (float)$t->nominal);
        $rasio_inflasi = $totalLastMonth != 0 ? (($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100 : 0;

        $totalMasukDD = DanaDarurat::where('id_user', $userId)->where('jenis_transaksi_dana_darurat', 1)->sum('nominal_dana_darurat');
        $totalKeluarDD = DanaDarurat::where('id_user', $userId)->where('jenis_transaksi_dana_darurat', 2)->sum('nominal_dana_darurat');
        $totalDanaDarurat = $totalMasukDD - $totalKeluarDD;

        $allTrx = Transaksi::where('id_user', $userId)->orderBy('tgl_transaksi')->get();
        $totalPengeluaranTotal = $allTrx->where('status', '1')->sum(fn($t) => (float)$t->nominal);
        $selisihBulan = ($allTrx->count() > 1) ? (Carbon::parse($allTrx->first()->tgl_transaksi)->startOfMonth()->diffInMonths(Carbon::parse($allTrx->last()->tgl_transaksi)->startOfMonth()) + 1) : 1;
        $rataRataPengeluaran = $selisihBulan > 0 ? $totalPengeluaranTotal / $selisihBulan : 0;
        $targetDanaDarurat = $rataRataPengeluaran * 6;
        $rasio_dana_darurat = $targetDanaDarurat > 0 ? ($totalDanaDarurat / $targetDanaDarurat) * 100 : 0;

        $totalPemasukanMonth = Transaksi::where('id_user', $userId)->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float)$t->nominal_pemasukan);
        $totalPengeluaranMonth = Transaksi::where('id_user', $userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float)$t->nominal);
        $rasio_pengeluaran_pendapatan = $totalPemasukanMonth > 0 ? ($totalPengeluaranMonth / $totalPemasukanMonth) * 100 : 0;

        $totalNominalToday = Transaksi::where('id_user', $userId)->whereDate('tgl_transaksi', Carbon::today())->get()->sum(fn($t) => (float)$t->nominal);
        $totalNominalMonthExp = Transaksi::where('id_user', $userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float)$t->nominal);
        $totalNominalMonthInc = Transaksi::where('id_user', $userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float)$t->nominal_pemasukan);
        $totalNominalSisa = $totalNominalMonthInc - $totalNominalMonthExp;

        $filterOptions = HasilProsesAnggaran::where('id_user', $userId)->select('tanggal_mulai', 'tanggal_selesai')->orderBy('tanggal_mulai', 'asc')->get()->unique(fn($row) => $row->tanggal_mulai . '_' . $row->tanggal_selesai)->values();

        $expenseStatus = $this->ratioStatus($expenseRatio, 'expense');
        $savingStatus = $this->ratioStatus($savingRateLatest, 'saving');
        $emergencyStatus = $this->ratioStatus($danaDaruratBulan, 'emergency');

        $bulan = (int) request('bulan', now()->month);
        $tahun = (int) request('tahun', now()->year);
        $pengeluaranKategori = Transaksi::with('pengeluaranRelation')->where('id_user', Auth::id())->whereMonth('tgl_transaksi', $bulan)->whereYear('tgl_transaksi', $tahun)->get()->filter(fn($t) => !empty($t->pengeluaran))->groupBy('pengeluaran')->map(fn($items) => (object)['kategori' => $items->first()->pengeluaranRelation->nama ?? 'Unknown', 'total' => $items->sum(fn($t) => (float)$t->nominal)])->sortByDesc('total')->values();
        $totalPengeluaranBulan = $pengeluaranKategori->sum('total');
        $pengeluaranKategori = $pengeluaranKategori->map(function ($row) use ($totalPengeluaranBulan) {
            $row->persen = $totalPengeluaranBulan > 0 ? round(($row->total / $totalPengeluaranBulan) * 100, 1) : 0;
            return $row;
        });

        $rawTransaksi = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])->where('id_user', Auth::id())->whereDate('tgl_transaksi', now())->orderByDesc('created_at')->limit(10)->get();
        $transaksiHariIni = collect();
        foreach ($rawTransaksi as $row) {
            if ((float)$row->nominal_pemasukan > 0) $transaksiHariIni->push((object)['waktu' => $row->created_at, 'jenis' => 'pemasukan', 'kategori' => $row->pemasukanRelation->nama ?? '-', 'keterangan' => $row->keterangan, 'nominal' => (float)$row->nominal_pemasukan]);
            if ($row->nominal > 0) $transaksiHariIni->push((object)['waktu' => $row->created_at, 'jenis' => 'pengeluaran', 'kategori' => $row->pengeluaranRelation->nama ?? '-', 'keterangan' => $row->keterangan, 'nominal' => (float)$row->nominal]);
        }
        $totalMasukHariIni = $transaksiHariIni->where('jenis', 'pemasukan')->sum(fn($t) => (float)$t->nominal);
        $totalKeluarHariIni = $transaksiHariIni->where('jenis', 'pengeluaran')->sum(fn($t) => (float)$t->nominal);

        $showNominal = session('show_nominal', false);
        $numbers = $this->getDashboardNumbers();
        session(['dashboard_numbers' => $numbers]);
        $saldoView = $this->maskNominal($numbers['saldo'], $showNominal);
        $pemasukanView = $this->maskNominal($numbers['pemasukan'], $showNominal);
        $pengeluaranView = $this->maskNominal($numbers['pengeluaran'], $showNominal);
        $pengeluaranHariIni = $this->maskNominal($numbers['hari_ini'], $showNominal);

        $persenSaldo = $numbers['persen_saldo'];
        $persenPemasukan = $numbers['persen_pemasukan'];
        $persenPengeluaran = $numbers['persen_pengeluaran'];

        return view('dashboard.index', compact('totalPinjaman', 'totalBarang', 'rasio', 'rasio_inflasi', 'rasio_dana_darurat', 'rasio_pengeluaran_pendapatan', 'totalNominalToday', 'totalNominalMonthExp', 'totalNominalMonthInc', 'totalNominalSisa', 'filterOptions', 'cashflow', 'savingRate', 'expenseRatio', 'savingRateLatest', 'danaDaruratBulan', 'expenseStatus', 'savingStatus', 'emergencyStatus', 'pengeluaranKategori', 'bulan', 'tahun', 'totalPengeluaranBulan', 'transaksiHariIni', 'totalMasukHariIni', 'totalKeluarHariIni', 'saldoView', 'pemasukanView', 'pengeluaranView', 'showNominal', 'pengeluaranHariIni', 'persenSaldo', 'persenPemasukan', 'persenPengeluaran'));
    }

    public function lineData()
    {
        $userId = Auth::id();
        $transaksi = Transaksi::where('id_user', $userId)->where('status', '1')->get();
        $data = [];
        foreach ($transaksi as $t) {
            $bulan_tahun = date('F Y', strtotime($t->tgl_transaksi));
            if (!isset($data[$bulan_tahun])) $data[$bulan_tahun] = ['pengeluaran' => 0, 'pemasukan' => 0];
            $data[$bulan_tahun]['pengeluaran'] += (float)$t->nominal;
            $data[$bulan_tahun]['pemasukan'] += (float)$t->nominal_pemasukan;
        }
        return response()->json(['labels' => array_keys($data), 'pengeluaran' => array_column($data, 'pengeluaran'), 'pemasukan' => array_column($data, 'pemasukan')]);
    }

    public function getChartData()
    {
        $userId = Auth::id();
        $transaksi = Transaksi::where('id_user', $userId)->where('status', '1')->get();
        $data = [];
        foreach ($transaksi as $t) {
            $bulan_tahun = date('F Y', strtotime($t->tgl_transaksi));
            if (!isset($data[$bulan_tahun])) $data[$bulan_tahun] = ['pengeluaran' => 0, 'pemasukan' => 0];
            $data[$bulan_tahun]['pengeluaran'] += (float)$t->nominal;
            $data[$bulan_tahun]['pemasukan'] += (float)$t->nominal_pemasukan;
        }
        return response()->json(['labels' => array_keys($data), 'data_pengeluaran' => array_column($data, 'pengeluaran'), 'data_pemasukan' => array_column($data, 'pemasukan')]);
    }

    public function getPieData()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $transaksi = Transaksi::where('id_user', $userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->filter(fn($t) => !empty($t->pengeluaran));
        $labels = [];
        $data = [];
        foreach ($transaksi as $t) {
            $pengeluaran = $t->pengeluaran;
            $nominal = (float)$t->nominal;
            $index = array_search($pengeluaran, $labels);
            if ($index !== false) $data[$index] += $nominal;
            else {
                $labels[] = $pengeluaran;
                $data[] = $nominal;
            }
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function TodayTransactions()
    {
        $userId = Auth::id();
        $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
        $endDate = Carbon::now('Asia/Jakarta')->endOfDay();
        $todayTransactions = Transaksi::with(['pengeluaranRelation', 'pemasukanRelation'])->where('id_user', $userId)->whereBetween('tgl_transaksi', [$startDate, $endDate])->orderBy('tgl_transaksi', 'desc')->get();
        return response()->json($todayTransactions);
    }

    public function getTransaksiByPengeluaran(Request $request)
    {
        try {
            $pengeluaran = $request->query('pengeluaran');
            $month = $request->query('month');
            $year = $request->query('year');
            if (!$pengeluaran || !$month || !$year) return response()->json(['error' => 'Parameter tidak lengkap'], 400);

            $data = Transaksi::with('pengeluaranRelation')->where('id_user', Auth::id())->whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->get()->filter(fn($t) => $t->pengeluaran == $pengeluaran)->map(fn($t) => (object)['tgl_transaksi' => $t->tgl_transaksi, 'keterangan' => $t->keterangan, 'nominal' => (float)$t->nominal, 'pengeluaran_nama' => $t->pengeluaranRelation->nama ?? 'Unknown'])->values();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    public function getSavingRateData(Request $request)
    {
        $userId = Auth::id();
        $periode = $request->query('periode', '6');
        if ($periode === 'all') {
            $startDate = Transaksi::where('id_user', $userId)->where('status', 2)->min('tgl_transaksi');
            $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->startOfMonth();
        } else {
            $start = Carbon::now()->subMonthsNoOverflow(intval($periode) - 1)->startOfMonth();
        }
        $end = Carbon::now()->endOfMonth();
        $transaksi = Transaksi::where('id_user', $userId)->where('status', 2)->whereBetween('tgl_transaksi', [$start, $end])->get();
        $data = [];
        $current = $start->copy();
        while ($current <= $end) {
            $label = $current->format('F Y');
            $data[$label] = 0;
            $current->addMonth();
        }
        foreach ($transaksi as $item) {
            $label = Carbon::parse($item->tgl_transaksi)->format('F Y');
            if (isset($data[$label])) $data[$label] += (float)$item->nominal;
        }
        return response()->json(['labels' => array_keys($data), 'data' => array_values($data)]);
    }

    public function AnggaranChart(Request $request)
    {
        $query = HasilProsesAnggaran::where('id_user', Auth::id());
        if ($request->filter) {
            [$mulai, $selesai] = explode('_', $request->filter);
            $query->where('tanggal_mulai', $mulai)->where('tanggal_selesai', $selesai);
        }
        $anggarans = $query->get();
        return response()->json(['labels' => $anggarans->pluck('nama_anggaran')->values(), 'ids' => $anggarans->pluck('id_proses_anggaran'), 'datasets' => [['name' => 'Anggaran', 'data' => $anggarans->pluck('nominal_anggaran')->map(fn($v) => (float)$v)->values()], ['name' => 'Realisasi', 'data' => $anggarans->pluck('anggaran_yang_digunakan')->map(fn($v) => (float)$v)->values()], ['name' => 'Sisa', 'data' => $anggarans->pluck('sisa_anggaran')->map(fn($v) => (float)$v)->values()]], 'table' => $anggarans]);
    }

    function logout()
    {
        Auth::logout();
        return redirect('/bimmo')->with('success', 'Successful Log out');
    }

    public function filter(Request $request)
    {
        if ($request->has('periode')) {
            $periode = (int)$request->periode;
            if (!in_array($periode, [2, 6, 12])) $periode = 6;
            $cashflow = Transaksi::where('id_user', Auth::id())->where('tgl_transaksi', '>=', now()->subMonths($periode - 1)->startOfMonth())->get()->groupBy(fn($t) => \Carbon\Carbon::parse($t->tgl_transaksi)->format('Y-m'))->map(fn($items, $bulan) => (object)['bulan' => $bulan, 'total_pemasukan' => $items->sum(fn($t) => (float)$t->nominal_pemasukan), 'total_pengeluaran' => $items->sum(fn($t) => (float)$t->nominal), 'selisih' => $items->sum(fn($t) => (float)$t->nominal_pemasukan) - $items->sum(fn($t) => (float)$t->nominal)])->sortBy('bulan')->values();
            
            $savingRateStatus = function ($rate) {
                if ($rate > 20) return ['label' => 'Sangat Sehat', 'class' => 'success'];
                if ($rate >= 10) return ['label' => 'Sehat', 'class' => 'primary'];
                if ($rate >= 0) return ['label' => 'Waspada', 'class' => 'warning'];
                return ['label' => 'Defisit', 'class' => 'danger'];
            };

            $savingRate = $cashflow->map(function ($row) use ($savingRateStatus) {
                $pendapatan = (float) $row->total_pemasukan;
                $pengeluaran = (float) $row->total_pengeluaran;
                $rate = $pendapatan > 0 ? round((($pendapatan - $pengeluaran) / $pendapatan) * 100, 2) : 0;
                $status = $savingRateStatus($rate);
                $row->saving_rate = $rate;
                $row->saving_label = $status['label'];
                $row->saving_class = $status['class'];
                return $row;
            });

            $chartData = [
                'cashflow' => $cashflow->map(fn($row) => [
                    'bulan' => \Carbon\Carbon::parse($row->bulan . '-01')->translatedFormat('F Y'),
                    'total_pemasukan' => $row->total_pemasukan,
                    'total_pengeluaran' => $row->total_pengeluaran
                ]),
                'savingRate' => $savingRate->map(fn($row) => [
                    'bulan' => \Carbon\Carbon::parse($row->bulan . '-01')->translatedFormat('F Y'),
                    'saving_rate' => $row->saving_rate
                ])
            ];

            return response()->json([
                'cashflow' => view('dashboard.partials.cashflow-table', compact('cashflow'))->render(),
                'savingRate' => view('dashboard.partials.saving-rate-table', compact('savingRate'))->render(),
                'chartData' => $chartData
            ]);
        }
        if ($request->has(['bulan', 'tahun'])) {
            $bulan = (int)$request->bulan;
            $tahun = (int)$request->tahun;
            $pengeluaranKategori = Transaksi::with('pengeluaranRelation')->where('id_user', Auth::id())->whereMonth('tgl_transaksi', $bulan)->whereYear('tgl_transaksi', $tahun)->get()->filter(fn($t) => !empty($t->pengeluaran))->groupBy('pengeluaran')->map(fn($items) => (object)['kategori' => $items->first()->pengeluaranRelation->nama ?? 'Unknown', 'total' => $items->sum(fn($t) => (float)$t->nominal)])->sortByDesc('total')->values();
            $totalPengeluaranBulan = $pengeluaranKategori->sum('total');
            $pengeluaranKategori = $pengeluaranKategori->map(function ($row) use ($totalPengeluaranBulan) {
                $row->persen = $totalPengeluaranBulan > 0 ? round(($row->total / $totalPengeluaranBulan) * 100, 1) : 0;
                return $row;
            });
            return response()->json(['expenseBar' => view('dashboard.partials.expense-bar-table', compact('pengeluaranKategori', 'totalPengeluaranBulan'))->render(), 'totalPengeluaran' => number_format((float)$totalPengeluaranBulan, 0, ',', '.')]);
        }
        return response()->json(['error' => 'Invalid request'], 400);
    }
}
