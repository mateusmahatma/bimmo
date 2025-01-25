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
            // Mendapatkan ID pengguna yang sedang login
            $userId = Auth::id();

            // Filter data berdasarkan id_user
            $query = Barang::where('id_user', $userId);

            $status = $request->status;

            if ($status === 'terbeli') {
                $query = $query->where('status', 'terbeli');
            } elseif ($status === 'belum terbeli') {
                $query = $query->where('status', '!=', 'terbeli');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($barang) {
                    return view('barang.tombol')->with('request', $barang);
                })
                ->toJson();
        } else {
            return view('barang.index');
        }
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'string',
            'status' => 'in:terbeli,belum terbeli',
            'nama_toko' => 'string',
            'harga' => 'numeric',
            'jumlah' => 'integer',
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
            'status' => 'in:terbeli,belum terbeli',
            'nama_toko' => 'string',
            'harga' => 'numeric',
            'jumlah' => 'integer',
        ]);

        Barang::where('id', $id)->update($validatedData);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }

    public function destroy($id)
    {
        $id = Barang::where('id', $id)->delete();
    }
}
