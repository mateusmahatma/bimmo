<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();

            $query = Pengeluaran::where('id_user', $userId);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '-';
                })
                ->addColumn('aksi', function ($request) {
                    return view('pengeluaran.tombol')->with('request', $request);
                })
                ->toJson();
        }
        return view('pengeluaran.index');
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

        $validatedData['id_user'] = Auth::id();

        pengeluaran::create($validatedData);
        return redirect('/pengeluaran');
    }

    public function show()
    {
        //
    }

    public function edit($id)
    {
        $data = Pengeluaran::where('id', $id)->first();
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

        Pengeluaran::where('id', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }

    public function destroy($id)
    {
        $id = Pengeluaran::where('id', $id)->delete();
    }
}
