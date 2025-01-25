<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;


class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();
            $data = Pinjaman::where('id_user', $userId);
            $totalPinjaman = $data->sum('jumlah_pinjaman');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('jumlah_pinjaman', function ($pinjaman) {
                    return 'Rp ' . number_format($pinjaman->jumlah_pinjaman, 0, ',', '.');
                })
                ->editColumn('jangka_waktu', function ($pinjaman) {
                    return $pinjaman->jangka_waktu . ' bulan';
                })
                ->editColumn('status', function ($pinjaman) {
                    return $pinjaman->status;
                })
                ->addColumn('aksi', function ($pinjaman) {
                    return view('pinjaman.tombol', ['pinjaman' => $pinjaman]);
                })

                ->with('totalPinjaman', 'Rp ' . number_format($totalPinjaman, 0, ',', '.'))
                ->rawColumns(['aksi'])
                ->toJson();
        }
        return view('pinjaman.index', [
            'pinjaman' => Pinjaman::where('id_user', Auth::id())->get(),
        ]);
    }

    public function create()
    {
        return view('pinjaman.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'required|numeric',
            'jangka_waktu' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:lunas,belum_lunas',
        ]);

        $validatedData['id_user'] = Auth::id();

        Pinjaman::create($validatedData);

        return redirect()->route('pinjaman.index')->with('success', 'Pinjaman Berhasil Tersimpan.');
    }

    public function show($id)
    {
        $pinjaman = Pinjaman::with('user', 'bayar_pinjaman')->findOrFail($id);
        return view('pinjaman.show', compact('pinjaman'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'required|numeric',
        ]);

        $pinjaman = Pinjaman::findOrFail($id);

        $pinjaman->nama_pinjaman = $validatedData['nama_pinjaman'];
        $pinjaman->jumlah_pinjaman = $validatedData['jumlah_pinjaman'];

        $pinjaman->save();

        return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pinjaman = Pinjaman::find($id);

        if (!$pinjaman) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $pinjaman->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }
}
