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
            'nama_barang' => 'string',
            'status' => 'required|in:0,1',
            'nama_toko' => 'string',
            'harga' => 'numeric',
        ]);

        $validatedData['id_user'] = Auth::id();

        Barang::create($validatedData);
        return redirect('/barang');
    }


    public function edit($id)
    {
        $data = Barang::where('id', $id)->first();
        return response()->json(['result' => $data]);
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
        return response()->json(['success' => "Berhasil melakukan update data"]);
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
