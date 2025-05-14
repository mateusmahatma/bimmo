<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Pinjaman;
use App\Models\Barang;
use App\Models\DanaDarurat;
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
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal');

        // Total nominal bulan lalu
        $totalLastMonth = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $lastMonth->year)
            ->whereMonth('tgl_transaksi', $lastMonth->month)
            ->sum('nominal');

        // Total nominal pemasukan bulan ini
        $totalPemasukanThisMonth = Transaksi::where('id_user', $userId)
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
        $totalPengeluaran = $transaksi->sum('nominal');

        // Hitung selisih bulan dari transaksi pertama ke terakhir
        if ($transaksi->count() > 1) {
            $firstDate = Carbon::parse($transaksi->first()->tgl_transaksi)->startOfMonth();
            $lastDate = Carbon::parse($transaksi->last()->tgl_transaksi)->startOfMonth();
            $selisihBulan = $firstDate->diffInMonths($lastDate) + 1; // +1 supaya bulan awal ikut dihitung
        } else {
            $selisihBulan = 1; // fallback kalau cuma ada 1 data
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
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal');

        $totalNominalBulanPemasukan = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal_pemasukan');

        $totalNominalSisa = $totalNominalBulanPemasukan - $totalNominalBulan;

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
        ));
    }

    // Cash Flow
    public function lineData()
    {
        $userId = Auth::id();
        $transaksi = Transaksi::where('id_user', $userId)->get();

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

        $todayTransactions = Transaksi::where('id_user', $userId)
            ->whereBetween('tgl_transaksi', [$startDate, $endDate])
            ->orderBy('tgl_transaksi', 'desc')
            ->get();

        return response()->json($todayTransactions);
    }

    public function getJenisPengeluaran(Request $request)
    {
        $userId = Auth::id();
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $jenisPengeluaran = Transaksi::select('pengeluaran', DB::raw('SUM(nominal) as total'))
            ->where('id_user', $userId)
            ->whereYear('tgl_transaksi', $selectedYear)
            ->whereMonth('tgl_transaksi', $selectedMonth)
            ->groupBy('pengeluaran')
            ->orderByDesc('total')
            ->get();

        return response()->json($jenisPengeluaran);
    }

    public function getTransaksiByPengeluaran(Request $request)
    {
        $pengeluaran = $request->query('pengeluaran');
        $month = $request->query('month');
        $year = $request->query('year');

        if (!$pengeluaran || !$month || !$year) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 400);
        }

        $data = Transaksi::where('id_user', Auth::id())
            ->select('tgl_transaksi', 'keterangan', 'nominal')
            ->where('pengeluaran', $pengeluaran)
            ->whereMonth('tgl_transaksi', $month)
            ->whereYear('tgl_transaksi', $year)
            ->get();

        return response()->json($data);
    }

    function logout()
    {
        Auth::logout();
        return redirect('/pointech')->with('success', 'Berhasil Logout');
    }
}
