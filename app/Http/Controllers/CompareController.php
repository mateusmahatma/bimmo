<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Transaksi;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\Auth;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Mendapatkan ID pengguna yang sedang login
            $userId = Auth::id();

            // Filter data berdasarkan id_user
            $data = Transaksi::where('id_user', $userId);

            $startDate1 = Carbon::parse($request->start_date_1)->toDateString();
            $endDate1 = Carbon::parse($request->end_date_1)->toDateString();
            $startDate2 = Carbon::parse($request->start_date_2)->toDateString();
            $endDate2 = Carbon::parse($request->end_date_2)->toDateString();
            $jenisPengeluaran = $request->pengeluaran;

            // Total Nominal Periode 1
            $totalNominal1 = Transaksi::where('id_user', $userId)
                ->whereBetween('tgl_transaksi', [$startDate1, $endDate1])
                ->when($jenisPengeluaran, function ($query) use ($jenisPengeluaran) {
                    return $query->where('pengeluaran', $jenisPengeluaran);
                })
                ->sum('nominal');

            // Total Nominal Periode 2
            $totalNominal2 = Transaksi::where('id_user', $userId)
                ->whereBetween('tgl_transaksi', [$startDate2, $endDate2])
                ->when($jenisPengeluaran, function ($query) use ($jenisPengeluaran) {
                    return $query->where('pengeluaran', $jenisPengeluaran);
                })
                ->sum('nominal');

            // Selisih
            $gap = $totalNominal1 - $totalNominal2;

            // Warna Teks
            $color = $gap < 0 ? 'red' : 'green';

            $response = [
                'data' => [
                    [
                        'nominalPeriode1' => $totalNominal1,
                        'nominalPeriode2' => $totalNominal2,
                        'gap' => $gap,
                        'color' => $color
                    ]
                ],
                'message' => 'Comparison calculated successfully'
            ];

            return response()->json($response);
        } else {
            return view('transaksi.compare', [
                'transaksi' => Transaksi::where('id_user', Auth::id())->get(),
                'pemasukan' => Pemasukan::where('id_user', Auth::id())->get(),
                'pengeluaran' => Pengeluaran::where('id_user', Auth::id())->get(),
            ])->with('message', 'Pastikan format tanggal yang Anda kirimkan adalah YYYY-MM-DD.');
        }
    }
}
