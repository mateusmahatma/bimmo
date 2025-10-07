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

        // 🔹 DataTables AJAX request (bukan PJAX)
        if ($request->ajax() && !$request->pjax()) {
            $query = Anggaran::where('id_user', $userId);

            $totalPersentase = (clone $query)->sum('persentase_anggaran');

            $exceedMessage = null;
            if ($totalPersentase > 100) {
                $exceedMessage = 'Persentase anggaran melebihi 100%!';
            } elseif ($totalPersentase < 100) {
                $exceedMessage = 'Persentase anggaran kurang dari 100%!';
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('nama_anggaran', fn($row) => $row->nama_anggaran)   // 👈 ini wajib ada, karena di JS kamu minta `nama_anggaran`
                ->addColumn('persentase_anggaran', fn($row) => $row->persentase_anggaran)
                ->addColumn('nama_pengeluaran', fn($row) => $row->nama_pengeluaran)
                ->addColumn('aksi', fn($row) => view('anggaran.tombol', ['request' => $row])->render())
                ->rawColumns(['aksi']) // biar tombol HTML tidak di-escape
                ->with('totalPersentase', $totalPersentase)
                ->with('exceedMessage', $exceedMessage)
                ->toJson();
        }


        // 🔹 Ambil ID pengeluaran yang sudah dipakai
        $usedPengeluaranIds = Anggaran::where('id_user', $userId)
            ->whereNotNull('id_pengeluaran')
            ->pluck('id_pengeluaran')
            ->map(function ($val) {
                // kalau tersimpan JSON array → decode dulu
                if (is_string($val) && str_starts_with($val, '[')) {
                    return json_decode($val, true);
                }
                return $val;
            })
            ->flatten()                        // ratakan nested array
            ->filter(fn($id) => is_numeric($id)) // hanya angka
            ->unique()
            ->values()
            ->toArray();

        $pengeluarans = Pengeluaran::where('id_user', $userId)
            ->when(!empty($usedPengeluaranIds), function ($q) use ($usedPengeluaranIds) {
                $q->whereNotIn('id', $usedPengeluaranIds);
            })
            ->get();

        $anggaran = new Anggaran();

        // 🔹 Kalau normal (refresh page) → balikin full view
        return view('anggaran.index', compact('anggaran', 'pengeluarans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_anggaran' => ['required', 'min:3', 'max:255'],
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

        // Pastikan id_pengeluaran berupa array
        $idPengeluaran = is_string($data->id_pengeluaran)
            ? json_decode($data->id_pengeluaran, true)
            : ($data->id_pengeluaran ?? []);

        // Ambil nama pengeluaran dari tabel pengeluaran
        $pengeluaranList = Pengeluaran::whereIn('id', $idPengeluaran)
            ->get(['id', 'nama']);

        // Buat array id => nama
        $data->id_pengeluaran = $pengeluaranList->mapWithKeys(function ($item) {
            return [$item->id => $item->nama];
        })->toArray();

        return response()->json(['result' => $data]);
    }



    public function update(Request $request, $id)
    {
        $validasi = Validator::make($request->all(), [
            'nama_anggaran' => 'required',
        ], [
            'nama_anggaran.required' => 'Diperlukan nama anggaran',
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
