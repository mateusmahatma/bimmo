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
        if ($request->ajax()) {
            $userId = Auth::id();
            $data = HasilProsesAnggaran::where('id_user', $userId)->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('hash', fn ($row) => Hashids::encode($row->id_proses_anggaran))
                ->addColumn('nama_jenis_pengeluaran', function ($row) {
                    $ids = $row->jenis_pengeluaran ?? [];
                    return Pengeluaran::whereIn('id', $ids)->pluck('nama')->toArray();
                })
                ->addColumn('sisa_anggaran', function ($row) {
                    $sisa = floatval($row->nominal_anggaran) - floatval($row->anggaran_yang_digunakan);
                    $row->sisa_anggaran = $sisa; $row->save();
                    return number_format($sisa, 0, ',', '.');
                })
                ->addColumn('aksi', fn ($request) => view('kalkulator.tombol')->with('request', $request))
                ->rawColumns(['aksi'])->toJson();
        }
        return view('kalkulator.index', ['hasilProses' => HasilProsesAnggaran::where('id_user', Auth::id())->get()]);
    }

    public function store(Request $request)
    {
        $request->validate(['monthly_income' => 'required|numeric', 'tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date']);
        $userId = Auth::id();
        $totalIncome = (float)$request->input('monthly_income') + (float)($request->input('additional_income') ?? 0);
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        $anggarans = Anggaran::where('id_user', $userId)->whereNotNull('id_pengeluaran')->get();
        foreach ($anggarans as $anggaran) {
            $jenisPengeluaran = is_array($anggaran->id_pengeluaran) ? $anggaran->id_pengeluaran : json_decode($anggaran->id_pengeluaran, true);
            if (!is_array($jenisPengeluaran)) $jenisPengeluaran = [$anggaran->id_pengeluaran];

            $totalTransaksi = Transaksi::where('id_user', $userId)->whereBetween('tgl_transaksi', [$tanggal_mulai, $tanggal_selesai])->get()->filter(fn($t)=>in_array($t->pengeluaran, $jenisPengeluaran))->sum(fn($t)=>(float)$t->nominal);
            $nominal = ($anggaran->persentase_anggaran / 100) * $totalIncome;

            HasilProsesAnggaran::create(['tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai, 'nama_anggaran' => $anggaran->nama_anggaran, 'jenis_pengeluaran' => $anggaran->id_pengeluaran, 'persentase_anggaran' => $anggaran->persentase_anggaran, 'nominal_anggaran' => $nominal, 'anggaran_yang_digunakan' => $totalTransaksi, 'id_user' => $userId]);
        }
        return response()->json(['success' => true, 'message' => 'Data anggaran berhasil diproses.', 'redirect' => url('/kalkulator')]);
    }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? abort(404);
        if ($request->ajax()) {
            $prosesAnggaran = HasilProsesAnggaran::find($id);
            if (!$prosesAnggaran) return response()->json(['error' => 'Data tidak ditemukan'], 404);
            $prosesAnggaran->fill($request->all());

            $jenisPengeluaran = $prosesAnggaran->jenis_pengeluaran;
            if (is_string($jenisPengeluaran)) { $decoded = json_decode($jenisPengeluaran, true); $jenisPengeluaran = is_array($decoded) ? $decoded : [$jenisPengeluaran]; }
            elseif (is_int($jenisPengeluaran)) $jenisPengeluaran = [$jenisPengeluaran];

            $totalTransaksi = Transaksi::where('id_user', $prosesAnggaran->id_user)->whereBetween('tgl_transaksi', [$prosesAnggaran->tanggal_mulai, $prosesAnggaran->tanggal_selesai])->get()->filter(fn($t)=>in_array($t->pengeluaran, $jenisPengeluaran))->sum(fn($t)=>(float)$t->nominal);
            $prosesAnggaran->anggaran_yang_digunakan = $totalTransaksi; $prosesAnggaran->save();

            return response()->json(['id' => Hashids::encode($prosesAnggaran->id_proses_anggaran), 'anggaran_digunakan_terkini' => number_format($totalTransaksi, 0, ',', '.'), 'sisa_anggaran' => number_format(floatval($prosesAnggaran->nominal_anggaran) - $totalTransaksi, 0, ',', '.')]);
        }
    }

    public function calculate(Request $request)
    {
        $totalIncome = (float)$request->input('monthly_income') + (float)$request->input('additional_income');
        $anggarans = Anggaran::where('id_user', Auth::id())->get();
        $budgetAllocations = [];
        foreach ($anggarans as $anggaran) { $budgetAllocations[] = ['nama_anggaran' => $anggaran->nama_anggaran, 'persentase_anggaran' => $anggaran->persentase_anggaran, 'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome]; }
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
        $decodedIds = array_filter(array_map(fn($hash)=>Hashids::decode($hash)[0]??null, $ids));
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
            if (is_string($idPengeluaranList)) { $decoded = json_decode($idPengeluaranList, true); $idPengeluaranList = is_array($decoded) ? $decoded : [$idPengeluaranList]; }
            else { $idPengeluaranList = [$idPengeluaranList]; }
        }

        if ($request->ajax()) {
            $transaksiColl = Transaksi::with('pengeluaranRelation')->where('id_user', $HasilProsesAnggaran->id_user)->whereDate('tgl_transaksi', '>=', $HasilProsesAnggaran->tanggal_mulai)->whereDate('tgl_transaksi', '<=', $HasilProsesAnggaran->tanggal_selesai)->get()->filter(fn($t)=>in_array($t->pengeluaran, $idPengeluaranList))->sortBy('tgl_transaksi')->values();
            return DataTables::of($transaksiColl)->addIndexColumn()->addColumn('nama', fn($trx)=>$trx->pengeluaranRelation->nama??'-')->editColumn('tgl_transaksi', fn($trx)=>$trx->tgl_transaksi)->editColumn('nominal', fn($trx)=>$trx->nominal)->editColumn('keterangan', fn($trx)=>$trx->keterangan??'-')->make(true);
        }
        $namaPengeluaran = Pengeluaran::whereIn('id', $idPengeluaranList)->pluck('nama')->toArray(); $total = count($namaPengeluaran);
        return view('kalkulator.show', compact('HasilProsesAnggaran', 'total', 'namaPengeluaran'));
    }
}
