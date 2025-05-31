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

class FinancialCalculatorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $userId = Auth::id();

            // Ambil data hasil proses anggaran untuk user yg sedang login, urutkan terbaru
            $data = HasilProsesAnggaran::where('id_user', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_pengeluaran', function ($row) {
                    // Pastikan kolom ini benar ada
                    return $row->nama_anggaran; // atau relasi jika ada
                })
                ->addColumn('sisa_anggaran', function ($row) {
                    $nominal = floatval($row->nominal_anggaran);
                    $digunakan = floatval($row->anggaran_yang_digunakan);
                    $sisa = $nominal - $digunakan;
                    return number_format($sisa, 0, ',', '.');
                })
                ->addColumn('aksi', function ($request) {
                    return view('kalkulator.tombol')->with('request', $request);
                })
                ->rawColumns(['aksi'])
                ->toJson();
        }
        return view('kalkulator.index', [
            'hasilProses' => HasilProsesAnggaran::where('id_user', Auth::id())->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'monthly_income' => 'required|numeric',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        $userId = Auth::id();
        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income') ?? 0;
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $totalIncome = $monthly_income + $additional_income;

        // Ambil semua anggaran user
        $anggarans = Anggaran::where('id_user', $userId)
            ->whereNotNull('id_pengeluaran')
            ->get();

        foreach ($anggarans as $anggaran) {
            // Pastikan ID pengeluaran dikonversi ke array
            $jenisPengeluaran = is_array($anggaran->id_pengeluaran)
                ? $anggaran->id_pengeluaran
                : json_decode($anggaran->id_pengeluaran, true);

            if (!is_array($jenisPengeluaran)) {
                $jenisPengeluaran = [$anggaran->id_pengeluaran];
            }

            // Hitung total transaksi dalam rentang tanggal
            $totalTransaksi = Transaksi::whereIn('pengeluaran', $jenisPengeluaran)
                ->whereBetween('tgl_transaksi', [$tanggal_mulai, $tanggal_selesai])
                ->sum('nominal');

            $nominal = ($anggaran->persentase_anggaran / 100) * $totalIncome;

            HasilProsesAnggaran::create([
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'nama_anggaran' => $anggaran->nama_anggaran,
                'jenis_pengeluaran' => $anggaran->id_pengeluaran,
                'persentase_anggaran' => $anggaran->persentase_anggaran,
                'nominal_anggaran' => $nominal,
                'anggaran_yang_digunakan' => $totalTransaksi,
                'id_user' => $userId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data anggaran berhasil diproses.',
            'redirect' => url('/kalkulator')
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            $prosesAnggaran = HasilProsesAnggaran::find($id);

            if (!$prosesAnggaran) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }

            // Update data sesuai request, jika ada
            $prosesAnggaran->fill($request->all());

            // Pastikan jenis_pengeluaran berupa array untuk whereIn()
            $jenisPengeluaran = $prosesAnggaran->jenis_pengeluaran;

            if (is_string($jenisPengeluaran)) {
                $decoded = json_decode($jenisPengeluaran, true);
                if (is_array($decoded)) {
                    $jenisPengeluaran = $decoded;
                } else {
                    $jenisPengeluaran = [$jenisPengeluaran];
                }
            } elseif (is_int($jenisPengeluaran)) {
                $jenisPengeluaran = [$jenisPengeluaran];
            }

            // Hitung total transaksi terbaru sesuai filter
            $totalTransaksi = Transaksi::whereIn('pengeluaran', $jenisPengeluaran)
                ->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])
                ->sum('nominal');

            $prosesAnggaran->anggaran_yang_digunakan = $totalTransaksi;
            $prosesAnggaran->save();

            $sisaAnggaran = floatval($prosesAnggaran->nominal_anggaran) - $totalTransaksi;

            return response()->json([
                'id' => $prosesAnggaran->id,
                'nama_pengeluaran' => $prosesAnggaran->nama_anggaran,
                'anggaran_digunakan_terkini' => number_format($totalTransaksi, 0, ',', '.'),
                'sisa_anggaran' => number_format($sisaAnggaran, 0, ',', '.'),
            ]);
        }
    }


    public function calculate(Request $request)
    {
        $userId = Auth::id();
        $query = Anggaran::where('id_user', $userId);

        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income');
        $totalIncome = $monthly_income + $additional_income;

        $Anggaran = $query->get();

        $budgetAllocations = [];

        foreach ($Anggaran as $anggaran) {
            $budgetAllocations[] = [
                'nama_anggaran' => $anggaran->nama_anggaran,
                'persentase_anggaran' => $anggaran->persentase_anggaran,
                'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome
            ];
        }

        $totalBudget = array_sum(array_column($budgetAllocations, 'nominal'));

        $remainingIncome = $totalIncome - $totalBudget;

        Session::put('budgetAllocations', $budgetAllocations);
        Session::put('totalBudget', $totalBudget);
        Session::put('totalIncome', $totalIncome);
        Session::put('remainingIncome', $remainingIncome);


        return view('kalkulator.result', [
            'totalIncome' => $totalIncome,
            'budgetAllocations' => $budgetAllocations,
            'totalBudget' => $totalBudget,
            'remainingIncome' => $remainingIncome
        ]);
    }

    public function showResult(Request $request)
    {
        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income');
        $totalIncome = $monthly_income + $additional_income;

        $budgetAllocations = Session::get('budgetAllocations', function () use ($totalIncome) {
            $Anggaran = Anggaran::all();
            $allocations = [];
            foreach ($Anggaran as $anggaran) {
                $allocations[] = [
                    'nama_anggaran' => $anggaran->nama_anggaran,
                    'persentase_anggaran' => $anggaran->persentase_anggaran,
                    'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome
                ];
            }
            return $allocations;
        });

        $totalBudget = Session::get('totalBudget', array_sum(array_column($budgetAllocations, 'nominal')));

        $totalIncome = Session::get('totalIncome', $totalIncome);

        $remainingIncome = $totalIncome - $totalBudget;

        return view('kalkulator.result', [
            'totalIncome' => $totalIncome,
            'budgetAllocations' => $budgetAllocations,
            'totalBudget' => $totalBudget,
            'remainingIncome' => $remainingIncome
        ]);
    }

    public function cetak_pdf(Request $request)
    {
        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income');
        $totalIncome = $monthly_income + $additional_income;

        $budgetAllocations = Session::get('budgetAllocations', function () use ($totalIncome) {
            $Anggaran = Anggaran::all();
            $allocations = [];
            foreach ($Anggaran as $anggaran) {
                $allocations[] = [
                    'nama_anggaran' => $anggaran->nama_anggaran,
                    'persentase_anggaran' => $anggaran->persentase_anggaran,
                    'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome
                ];
            }
            return $allocations;
        });

        $totalBudget = Session::get('totalBudget', array_sum(array_column($budgetAllocations, 'nominal')));

        $totalIncome = Session::get('totalIncome', $totalIncome);

        $remainingIncome = $totalIncome - $totalBudget;

        $data = [
            'totalIncome' => $totalIncome,
            'budgetAllocations' => $budgetAllocations,
            'totalBudget' => $totalBudget,
            'remainingIncome' => $remainingIncome
        ];
        $pdf = PDF::loadview('Kalkulator.pdf', $data);
        return $pdf->stream('');
    }

    public function destroy($id)
    {
        $deleted = HasilProsesAnggaran::where('id_proses_anggaran', $id)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Data berhasil dihapus']);
        }

        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }
}
