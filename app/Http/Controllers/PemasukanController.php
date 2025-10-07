<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Mendapatkan ID pengguna yang sedang login
            $userId = Auth::id();

            $query = Pemasukan::where('id_user', $userId);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('aksi', function ($pemasukan) {
                    return view('pemasukan.tombol')->with('request', $pemasukan);
                })
                ->toJson();
        }
        return view('pemasukan.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => ['required', 'min:3', 'max:255'],
        ]);

        // Tambahkan id_user ke data yang akan disimpan
        $validatedData['id_user'] = Auth::id();

        pemasukan::create($validatedData);
        return redirect('/pemasukan');
    }

    public function show() {}

    public function edit($id)
    {
        $data = pemasukan::where('id', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $id)
    {
        $validasi = Validator::make($request->all(), [
            'nama' => 'required',
        ], [
            'nama.required' => 'Nama wajib diisi',
        ]);

        if ($validasi->fails()) {
            return response()->json(['errors' => $validasi->errors()]);
        }

        $data = [
            'nama' => $request->nama,
        ];

        Pemasukan::where('id', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();
    }
}
