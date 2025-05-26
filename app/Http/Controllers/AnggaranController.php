<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Anggaran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengeluaran;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->ajax()) {
            $query = Anggaran::where('id_user', $userId);

            $totalPersentase = $query->sum('persentase_anggaran');

            $exceedMessage = null;
            if ($totalPersentase > 100) {
                $exceedMessage = 'Percentage of budget exceeds 100%!';
            } elseif ($totalPersentase < 100) {
                $exceedMessage = 'The budget percentage is less than 100%!';
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()

                ->addColumn('nama_pengeluaran', function ($anggaran) {
                    return $anggaran->nama_pengeluaran;
                })

                ->addColumn('aksi', function ($anggaran) {
                    return view('anggaran.tombol')->with('request', $anggaran);
                })

                ->with('totalPersentase', $totalPersentase)
                ->with('exceedMessage', $exceedMessage)
                ->toJson();
        }

        $anggaran = new Anggaran();
        // Dapatkan id_pengeluaran yang sudah dipakai di tabel anggaran milik user ini
        $usedPengeluaranIds = Anggaran::where('id_user', $userId)
            ->whereNotNull('id_pengeluaran')
            ->get()
            ->pluck('id_pengeluaran')
            ->flatten()
            ->unique()
            ->toArray();
        // Ambil pengeluaran yang belum dipakai
        $pengeluarans = Pengeluaran::where('id_user', $userId)
            ->whereNotIn('id', $usedPengeluaranIds)
            ->get();

        return view('anggaran.index', compact('anggaran', 'pengeluarans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_anggaran' => ['required', 'min:3', 'max:255', 'unique:anggaran'],
            'persentase_anggaran' => ['required', 'numeric', 'between:0,100'],
            'id_pengeluaran' => ['array'],
            'id_pengeluaran.*' => ['exists:pengeluaran,id'],
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
            'nama_anggaran.required' => 'Budget name required',
        ]);

        if ($validasi->fails()) {
            return response()->json(['errors' => $validasi->errors()]);
        }

        $data = [
            'nama_anggaran' => $request->nama_anggaran,
            'persentase_anggaran' => $request->persentase_anggaran,
            'id_pengeluaran' => $request->id_pengeluaran ? json_encode($request->id_pengeluaran) : null,
        ];

        Anggaran::where('id_anggaran', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }

    public function destroy($id)
    {
        $id = Anggaran::where('id_anggaran', $id)->delete();
    }
}
