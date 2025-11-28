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
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Ambil transaksi
        $transaksi = Transaksi::where('id_user', $userId)->get();

        // Hitung total pinjaman dan total barang
        $totalPinjaman = Pinjaman::where('id_user', $userId)->sum('jumlah_pinjaman');
        $totalBarang = Barang::where('id_user', $userId)
            ->where('status', '1')
            ->sum('harga');
        $rasio = $totalBarang > 0 ? ($totalPinjaman / $totalBarang) * 100 : 0;

        // rasio inflasi gaya hidup
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Total nominal bulan ini
        $totalThisMonth = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal');

        // Total nominal bulan lalu
        $totalLastMonth = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $lastMonth->year)
            ->whereMonth('tgl_transaksi', $lastMonth->month)
            ->sum('nominal');

        // Total nominal pemasukan bulan ini
        $totalPemasukanThisMonth = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal_pemasukan');

        // Total nominal pemasukan bulan lalu
        $totalPemasukanLastMonth = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $lastMonth->year)
            ->whereMonth('tgl_transaksi', $lastMonth->month)
            ->sum('nominal_pemasukan');

        // Hitung rasio inflasi gaya hidup
        if ($totalLastMonth != 0) {
            $rasio_inflasi = (($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
        } else {
            // Tangani kasus jika totalLastMonth adalah 0
            $rasio_inflasi = 0; // Atau nilai default lainnya sesuai kebutuhan
        }

        // Rasio Dana Darurat
        $totalMasuk = DanaDarurat::where('id_user', $userId)
            ->where('jenis_transaksi_dana_darurat', 1)
            ->sum('nominal_dana_darurat');

        $totalKeluar = DanaDarurat::where('id_user', $userId)
            ->where('jenis_transaksi_dana_darurat', 2)
            ->sum('nominal_dana_darurat');

        $totalDanaDarurat = $totalMasuk - $totalKeluar;

        // Ambil semua transaksi user
        $transaksi = Transaksi::where('id_user', $userId)->orderBy('tgl_transaksi')->get();

        // Hitung total pengeluaran
        $totalPengeluaran = $transaksi->where('status', '1')->sum('nominal');

        // Hitung selisih bulan dari transaksi pertama ke terakhir
        if ($transaksi->count() > 1) {
            $firstDate = Carbon::parse($transaksi->first()->tgl_transaksi)->startOfMonth();
            $lastDate = Carbon::parse($transaksi->last()->tgl_transaksi)->startOfMonth();
            $selisihBulan = $firstDate->diffInMonths($lastDate) + 1; // +1 supaya bulan awal ikut dihitung
        } else {
            // fallback kalau cuma ada 1 data
            $selisihBulan = 1;
        }

        // Rata-rata pengeluaran bulanan
        $rataRataPengeluaran = $selisihBulan > 0 ? $totalPengeluaran / $selisihBulan : 0;

        $targetDanaDarurat = $rataRataPengeluaran * 6;

        $rasio_dana_darurat = $targetDanaDarurat > 0 ? ($totalDanaDarurat / $targetDanaDarurat) * 100 : 0;
        // Rasio Dana Darurat

        // Rasio Pengeluaran Terhadap Pendapatan Bulan Ini
        $totalPemasukan = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal_pemasukan');

        $totalPengeluaran = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal');

        $rasio_pengeluaran_pendapatan = $totalPemasukan > 0 ? ($totalPengeluaran / $totalPemasukan) * 100 : 0;

        // Total Nominal Harian
        $today = Carbon::today();
        $totalNominal = Transaksi::where('id_user', $userId)
            ->whereDate('tgl_transaksi', $today)
            ->sum('nominal');

        // Total Nominal Bulanan
        $now = Carbon::now();
        $totalNominalBulan = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal');

        $totalNominalBulanPemasukan = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal_pemasukan');

        $totalNominalSisa = $totalNominalBulanPemasukan - $totalNominalBulan;


        // Ambil kombinasi tanggal unik untuk dropdown filter
        $filterOptions = HasilProsesAnggaran::where('id_user', $userId)
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->unique(fn($row) => $row->tanggal_mulai . '_' . $row->tanggal_selesai)
            ->values();

        // Kirim semua data ke view
        return view('dashboard.index', compact(
            'transaksi',
            'totalPinjaman',
            'totalBarang',
            'rasio',
            'rasio_inflasi',
            'rasio_dana_darurat',
            'rasio_pengeluaran_pendapatan',
            'totalNominal',
            'totalNominalBulan',
            'totalNominalBulanPemasukan',
            'totalNominalSisa',
            'filterOptions'
        ));
    }

    // Cash Flow
    public function lineData()
    {
        $userId = Auth::id();
        $transaksi = Transaksi::where('id_user', $userId)->where('status', '1')->get();

        $data = [];
        foreach ($transaksi as $transaksi) {
            $bulan_tahun = date('F Y', strtotime($transaksi->tgl_transaksi));

            if (!isset($data[$bulan_tahun])) {
                $data[$bulan_tahun] = [
                    'pengeluaran' => 0,
                    'pemasukan' => 0
                ];
            }

            $data[$bulan_tahun]['pengeluaran'] += $transaksi->nominal;
            $data[$bulan_tahun]['pemasukan'] += $transaksi->nominal_pemasukan;
        }

        $formattedData = [
            'labels' => array_keys($data),
            'pengeluaran' => array_column($data, 'pengeluaran'),
            'pemasukan' => array_column($data, 'pemasukan')
        ];

        return response()->json($formattedData);
    }

    // Expense Chart
    public function getChartData()
    {
        $userId = Auth::id();
        $transaksi = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->get();

        $data = [];
        foreach ($transaksi as $transaksi) {
            $bulan_tahun = date('F Y', strtotime($transaksi->tgl_transaksi));

            if (!isset($data[$bulan_tahun])) {
                $data[$bulan_tahun] = ['pengeluaran' => 0, 'pemasukan' => 0];
            }

            $data[$bulan_tahun]['pengeluaran'] += $transaksi->nominal;
            $data[$bulan_tahun]['pemasukan'] += $transaksi->nominal_pemasukan; // Pastikan ada kolom nominal_pemasukan
        }

        return response()->json([
            'labels' => array_keys($data),
            'data_pengeluaran' => array_column($data, 'pengeluaran'),
            'data_pemasukan' => array_column($data, 'pemasukan')
        ]);
    }

    public function getPieData()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $transaksi = Transaksi::where('id_user', $userId)
            ->where('status', '1')
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->where('pengeluaran', '!=', '')
            ->get();

        $labels = [];
        $data = [];

        foreach ($transaksi as $transaksi) {
            $pengeluaran = $transaksi->pengeluaran;
            $nominal = $transaksi->nominal;

            $index = array_search($pengeluaran, $labels);

            if ($index !== false) {
                $data[$index] += $nominal;
            } else {
                $labels[] = $pengeluaran;
                $data[] = $nominal;
            }
        }

        $pieChartData = [
            'labels' => $labels,
            'data' => $data,
        ];

        return response()->json($pieChartData);
    }

    public function TodayTransactions()
    {
        $userId = Auth::id();
        $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
        $endDate = Carbon::now('Asia/Jakarta')->endOfDay();

        $todayTransactions = Transaksi::with(['pengeluaranRelation', 'pemasukanRelation'])
            ->where('id_user', $userId)
            ->whereBetween('tgl_transaksi', [$startDate, $endDate])
            ->orderBy('tgl_transaksi', 'desc')
            ->get();

        return response()->json($todayTransactions);
    }

    // Expenses Bar
    public function getJenisPengeluaran(Request $request)
    {
        $userId = Auth::id();
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $jenisPengeluaran = Transaksi::select(
            'pengeluaran.id as pengeluaran_id',
            'pengeluaran.nama as pengeluaran_nama',
            DB::raw('SUM(nominal) as total')
        )
            ->join('pengeluaran', 'transaksi.pengeluaran', '=', 'pengeluaran.id')
            ->where('transaksi.id_user', $userId)
            ->where('transaksi.status', '1')
            ->whereYear('tgl_transaksi', $selectedYear)
            ->whereMonth('tgl_transaksi', $selectedMonth)
            ->groupBy('pengeluaran.id', 'pengeluaran.nama')
            ->orderByDesc('total')
            ->get();

        return response()->json($jenisPengeluaran);
    }

    // Detail Modal Expense Bar - Tetap sama
    public function getTransaksiByPengeluaran(Request $request)
    {
        try {
            $pengeluaran = $request->query('pengeluaran');
            $month = $request->query('month');
            $year = $request->query('year');

            if (!$pengeluaran || !$month || !$year) {
                return response()->json(['error' => 'Parameter tidak lengkap'], 400);
            }

            $data = Transaksi::select(
                'transaksi.tgl_transaksi',
                'transaksi.keterangan',
                'transaksi.nominal',
                'pengeluaran.nama as pengeluaran_nama'
            )
                ->join('pengeluaran', 'transaksi.pengeluaran', '=', 'pengeluaran.id')
                ->where('transaksi.id_user', Auth::id())
                ->where('transaksi.pengeluaran', $pengeluaran)
                ->whereMonth('transaksi.tgl_transaksi', $month)
                ->whereYear('transaksi.tgl_transaksi', $year)
                ->orderBy('transaksi.tgl_transaksi')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    // Saving Rate
    public function getSavingRateData(Request $request)
    {
        $userId = Auth::id();
        $periode = $request->query('periode', '6');

        if ($periode === 'all') {
            $startDate = Transaksi::where('id_user', $userId)
                ->where('status', 2)
                ->min('tgl_transaksi');

            $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->startOfMonth();
        } else {
            $months = intval($periode);
            $start = Carbon::now()->subMonthsNoOverflow($months - 1)->startOfMonth();
        }

        $end = Carbon::now()->endOfMonth();

        $transaksi = Transaksi::where('id_user', $userId)
            ->where('status', 2)
            ->whereBetween('tgl_transaksi', [$start, $end])
            ->get();

        // Inisialisasi bulan
        $data = [];
        $current = $start->copy();
        while ($current <= $end) {
            $label = $current->format('F Y');
            $data[$label] = 0;
            $current->addMonth();
        }

        foreach ($transaksi as $item) {
            $label = Carbon::parse($item->tgl_transaksi)->format('F Y');
            if (isset($data[$label])) {
                $data[$label] += $item->nominal;
            }
        }

        return response()->json([
            'labels' => array_keys($data),
            'data' => array_values($data),
        ]);
    }

    public function AnggaranChart(Request $request)
    {
        $userId = Auth::id();

        $query = HasilProsesAnggaran::where('id_user', $userId);

        // Jika filter aktif
        if ($request->filter) {
            [$mulai, $selesai] = explode('_', $request->filter);
            $query->where('tanggal_mulai', $mulai)
                ->where('tanggal_selesai', $selesai);
        }

        $anggarans = $query->get();

        return response()->json([
            'labels' => $anggarans->pluck('nama_anggaran')->values(),
            'ids' => $anggarans->pluck('id_proses_anggaran'),
            'datasets' => [
                [
                    'name' => 'Anggaran',
                    'data' => $anggarans->pluck('nominal_anggaran')->map(fn($v) => (float)$v)->values()
                ],
                [
                    'name' => 'Realisasi',
                    'data' => $anggarans->pluck('anggaran_yang_digunakan')->map(fn($v) => (float)$v)->values()
                ],
                [
                    'name' => 'Sisa',
                    'data' => $anggarans->pluck('sisa_anggaran')->map(fn($v) => (float)$v)->values()
                ]
            ],
            'table' => $anggarans // kirim data untuk tampil dalam list
        ]);
    }

    function logout()
    {
        Auth::logout();
        return redirect('/bimmo')->with('success', 'Successful Log out');
    }
}
