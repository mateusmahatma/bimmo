<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->ajax()) {

            $data = Transaksi::where('id_user', $userId)
                ->select('id, tgl_transaksi, pemasukan, nominal_pemasukan, pengeluaran, nominal, keterangan, id_user')
                ->get();

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $data = $data->whereBetween('tgl_transaksi', [$request->from_date, $request->to_date]);
            }
        }
        return view('dashboard.index', [
            'transaksi' => Transaksi::where('id_user', Auth::id())->get(),
        ]);
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

    public function showTotalNominal()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $monthStart = $now->startOfMonth();
        $monthEnd = $now->endOfMonth();
        $today = Carbon::today();

        $totalNominal = Transaksi::where('id_user', $userId)
            ->whereDate('tgl_transaksi', $today)
            ->sum('nominal');

        $totalNominalBulan = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal');

        $totalNominalBulanPemasukan = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->sum('nominal_pemasukan');

        $totalNominalSisa = $totalNominalBulanPemasukan - $totalNominalBulan;

        return view('dashboard.index', compact('totalNominal', 'totalNominalBulan', 'totalNominalBulanPemasukan', 'totalNominalSisa'));
    }

    function logout()
    {
        Auth::logout();
        return redirect('/pointech')->with('success', 'Berhasil Logout');
    }
}
