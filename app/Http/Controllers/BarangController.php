<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();
            $query = Barang::where('id_user', $userId);
            $status = $request->status;
            $totalBarang = $query->where('status', '1')->sum('harga');
            $query = Barang::where('id_user', $userId);
            if ($status === '1') {
                $query = $query->where('status', '1');
            } elseif ($status === '0') {
                $query = $query->where('status', '!=', '1');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($barang) {
                    return view('barang.tombol')->with('request', $barang);
                })
                ->with('totalBarang', 'Rp ' . number_format($totalBarang, 0, ',', '.'))
                ->toJson();
        } else {
            return view('barang.index');
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string',
            'status' => 'required|in:0,1',
            'nama_toko' => 'string',
            'harga' => 'numeric',
        ]);

        $validatedData['id_user'] = Auth::id();

        Barang::create($validatedData);
        return redirect('/barang')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function create()
    {
        return view('barang.create', [
            'barang' => new Barang(),
        ]);
    }

    public function edit($id)
    {
        $barang = Barang::where('id', $id)->first();

        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'string',
            'status' => 'in:1,0',
            'nama_toko' => 'string',
            'harga' => 'numeric',
        ]);

        Barang::where('id', $id)->update($validatedData);

        return redirect()->route('barang.index')
            ->with('success', 'Berhasil update Aset!');
    }

    public function destroy($id)
    {
        $id = Barang::where('id', $id)->delete();
    }

    public function getList()
    {
        $userId = Auth::id();
        $barang = Barang::where('status', 1)
            ->where('id_user', $userId)
            ->select('id', 'nama_barang')
            ->get();

        return response()->json($barang);
    }
}
