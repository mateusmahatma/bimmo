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
                ->addColumn('nama_anggaran', fn($row) => $row->nama_anggaran)
                ->addColumn('persentase_anggaran', fn($row) => $row->persentase_anggaran)
                ->addColumn('list_pengeluaran', function ($row) {
                    if (empty($row->id_pengeluaran)) return [];
                    return Pengeluaran::whereIn('id', $row->id_pengeluaran)
                        ->pluck('nama')
                        ->toArray();
                })
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

        return view('anggaran.index', compact('anggaran', 'pengeluarans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_anggaran' => ['required', 'string', 'min:3', 'max:255'],
            'persentase_anggaran' => ['required', 'numeric', 'min:0', 'max:100'],
            'id_pengeluaran' => ['required', 'nullable', 'array', 'min:1'],
            'id_pengeluaran.*' => ['exists:pengeluaran,id'],
        ], [
            'persentase_anggaran.max' => 'Secara keseluruhan persentase anggaran sudah melebihi 100% mohon dicek kembali.',
            'persentase_anggaran.min' => 'Persentase anggaran tidak boleh kurang dari 0%.',
            'persentase_anggaran.required' => 'Field persentase anggaran wajib diisi.',
            'id_pengeluaran.required' => 'Jenis pengeluaran harus dipilih.',
            'id_pengeluaran.min' => 'Pilih minimal satu jenis pengeluaran.',
        ]);

        $userId = Auth::id();
        $currentTotal = Anggaran::where('id_user', $userId)->sum('persentase_anggaran');
        $newTotal = $currentTotal + $request->persentase_anggaran;

        // Validasi total melebihi 100%
        if ($newTotal > 100) {
            return back()
                ->withErrors([
                    'persentase_anggaran' => 'Persentase anggaran sudah melebihi 100% mohon dicek kembali.',
                ])
                ->withInput();
        }

        $validatedData['id_user'] = $userId;
        Anggaran::create($validatedData);

        return redirect()->route('anggaran.index')
            ->with('success', 'Data berhasil disimpan!');
    }



    public function create()
    {
        $userId = Auth::id();

        $usedIds = Anggaran::where('id_user', $userId)
            ->pluck('id_pengeluaran')
            ->flatMap(function ($item) {
                // Jika sudah array → langsung return
                if (is_array($item)) {
                    return $item;
                }
                // Jika string JSON → decode
                if (is_string($item)) {
                    return json_decode($item, true) ?: [];
                }
                // Jika null → abaikan
                return [];
            })
            ->unique()
            ->toArray();

        $pengeluarans = Pengeluaran::where('id_user', $userId)
            ->when(!empty($usedIds), fn($q) => $q->whereNotIn('id', $usedIds))
            ->get();

        return view('anggaran.create', [
            'anggaran' => new Anggaran(),
            'pengeluarans' => $pengeluarans
        ]);
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
