<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Anggaran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();

            $query = Anggaran::where('id_user', $userId);

            $totalPersentase = $query->sum('persentase_anggaran');

            $exceedMessage = null;
            if ($totalPersentase > 100) {
                $exceedMessage = 'Persentase anggaran melebihi 100% !';
            } elseif ($totalPersentase < 100) {
                $exceedMessage = 'Persentase anggaran kurang dari 100%!';
            } else {
                $exceedMessage = null;
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($anggaran) {
                    return view('anggaran.tombol')->with('request', $anggaran);
                })
                ->with('totalPersentase', $totalPersentase)
                ->with('exceedMessage', $exceedMessage)
                ->toJson();
        }

        return view('anggaran.index');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_anggaran' => ['required', 'min:3', 'max:255', 'unique:anggaran'],
            'persentase_anggaran' => ['required', 'numeric', 'between:0,100'],
        ]);

        $validatedData['id_user'] = Auth::id();

        anggaran::create($validatedData);
        return redirect('/anggaran');
    }

    public function edit($id)
    {
        $data = Anggaran::where('id_anggaran', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $id)
    {
        $validasi = Validator::make($request->all(), [
            'nama_anggaran' => 'required',
        ], [
            'nama_anggaran.required' => 'Nama Anggaran wajib diisi',
        ]);

        if ($validasi->fails()) {
            return response()->json(['errors' => $validasi->errors()]);
        }

        $data = [
            'nama_anggaran' => $request->nama_anggaran,
            'persentase_anggaran' => $request->persentase_anggaran,
        ];

        Anggaran::where('id_anggaran', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }

    public function destroy($id)
    {
        $id = Anggaran::where('id_anggaran', $id)->delete();
    }
}
