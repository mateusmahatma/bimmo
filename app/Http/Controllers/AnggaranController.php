<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggaran;
use App\Models\Pengeluaran;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Anggaran::where('id_user', $userId);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_anggaran', 'LIKE', "%{$search}%");
        }

        // Summary calculations (before sorting/pagination)
        $totalPersentase = (clone $query)->sum('persentase_anggaran');
        $exceedMessage = null;
        if ($totalPersentase > 100) {
            $exceedMessage = 'Persentase anggaran melebihi 100%!';
        }
        elseif ($totalPersentase < 100 && $totalPersentase > 0) {
            $exceedMessage = 'Persentase anggaran kurang dari 100%!';
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSort = ['nama_anggaran', 'persentase_anggaran', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort)) {
            $query->orderBy($sort, $direction);
        }
        else {
            $query->orderBy('created_at', 'desc');
        }

        $anggarans = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('anggaran._table_list', [
                    'anggarans' => $anggarans,
                    'sort' => $sort,
                    'direction' => $direction
                ])->render(),
                'totalPersentase' => $totalPersentase,
                'exceedMessage' => $exceedMessage,
            ]);
        }

        // Ambil semua pengeluaran user (tidak difilter agar edit modal bisa menampilkan yang sudah terpilih)
        $pengeluarans = Pengeluaran::where('id_user', $userId)->get();

        $anggaran = new Anggaran();

        return view('anggaran.index', compact('anggarans', 'anggaran', 'pengeluarans', 'totalPersentase', 'exceedMessage', 'sort', 'direction'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_anggaran' => ['required', 'string', 'min:3', 'max:255'],
            'persentase_anggaran' => ['required', 'numeric', 'min:0', 'max:100'],
            'id_pengeluaran' => ['required', 'nullable', 'array', 'min:1'],
            'id_pengeluaran.*' => ['exists:pengeluaran,id'],
        ], [
            'nama_anggaran.required' => 'Nama Anggaran wajib diisi.',
            'persentase_anggaran.max' => 'Secara keseluruhan persentase anggaran sudah melebihi 100% mohon dicek kembali.',
            'persentase_anggaran.min' => 'Persentase anggaran tidak boleh kurang dari 0%.',
            'persentase_anggaran.required' => 'Persentase Anggaran wajib diisi.',
            'id_pengeluaran.required' => 'Jenis Pengeluaran wajib diisi.',
            'id_pengeluaran.min' => 'Pilih minimal satu jenis pengeluaran.',
        ]);

        $userId = Auth::id();
        $currentTotal = Anggaran::where('id_user', $userId)->sum('persentase_anggaran');
        $newTotal = $currentTotal + $request->persentase_anggaran;

        // Validasi total melebihi 100%
        if ($newTotal > 100) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['persentase_anggaran' => ['Persentase anggaran sudah melebihi 100% mohon dicek kembali.']]], 422);
            }
            return back()
                ->withErrors([
                'persentase_anggaran' => 'Persentase anggaran sudah melebihi 100% mohon dicek kembali.',
            ])
                ->withInput();
        }

        $validatedData['id_user'] = $userId;
        Anggaran::create($validatedData);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);
        }

        return redirect()->route('anggaran.index')
            ->with('success', 'Data berhasil disimpan!');
    }

    // ... create, edit methods remain same (view based) ...
    public function create()
    {
        $userId = Auth::id();

        $usedIds = Anggaran::where('id_user', $userId)
            ->pluck('id_pengeluaran')
            ->flatMap(function ($item) {
            if (is_array($item))
                return $item;
            if (is_string($item))
                return json_decode($item, true) ?: [];
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
        $userId = Auth::id();

        $anggaran = Anggaran::where('id_anggaran', $id)
            ->where('id_user', $userId)
            ->firstOrFail();

        // Decode id_pengeluaran agar menjadi array
        $selectedIds = is_string($anggaran->id_pengeluaran)
            ? json_decode($anggaran->id_pengeluaran, true)
            : ($anggaran->id_pengeluaran ?? []);

        // Tampilkan semua pengeluaran user agar bisa diubah
        $pengeluarans = Pengeluaran::where('id_user', $userId)->get();

        if (request()->ajax()) {
            return response()->json(['result' => $anggaran]);
        }

        return view('anggaran.edit', compact('anggaran', 'pengeluarans', 'selectedIds'));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $request->validate([
            'nama_anggaran' => ['required', 'string', 'min:3', 'max:255'],
            'persentase_anggaran' => ['required', 'numeric', 'min:0', 'max:100'],
            'id_pengeluaran' => ['nullable', 'array'],
            'id_pengeluaran.*' => ['exists:pengeluaran,id'],
        ], [
            'nama_anggaran.required' => 'Nama Anggaran wajib diisi.',
            'persentase_anggaran.max' => 'Secara keseluruhan persentase anggaran sudah melebihi 100% mohon dicek kembali.',
            'persentase_anggaran.min' => 'Persentase anggaran tidak boleh kurang dari 0%.',
            'persentase_anggaran.required' => 'Persentase Anggaran wajib diisi.',
            'id_pengeluaran.required' => 'Jenis Pengeluaran wajib diisi.',
            'id_pengeluaran.min' => 'Pilih minimal satu jenis pengeluaran.',
        ]);

        $anggaran = Anggaran::where('id_anggaran', $id)
            ->where('id_user', $userId)
            ->firstOrFail();

        $totalPersenTerpakai = Anggaran::where('id_user', $userId)
            ->where('id_anggaran', '!=', $id)
            ->sum('persentase_anggaran');

        $totalBaru = $totalPersenTerpakai + $request->persentase_anggaran;

        if ($totalBaru > 100) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['persentase_anggaran' => ['Persentase anggaran sudah melebihi 100% mohon dicek kembali']]], 422);
            }
            return back()
                ->withErrors([
                'persentase_anggaran' =>
                'Persentase anggaran sudah melebihi 100% mohon dicek kembali'
            ])
                ->withInput();
        }

        $anggaran->update([
            'nama_anggaran' => $request->nama_anggaran,
            'persentase_anggaran' => $request->persentase_anggaran,
            'id_pengeluaran' => $request->id_pengeluaran,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Berhasil update anggaran!']);
        }

        return redirect()->route('anggaran.index')
            ->with('success', 'Berhasil update anggaran!');
    }

    public function destroy($id)
    {
        $deleted = Anggaran::where('id_anggaran', $id)
            ->where('id_user', Auth::id())
            ->delete();

        if (request()->ajax()) {
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Data deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Data not found or unauthorized'], 404);
        }
        return redirect()->back();
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:anggaran,id_anggaran' // Adjusted to id_anggaran based on destroy method usage
        ]);

        $ids = $validated['ids'];

        // Ensure user owns these records
        $deleted = Anggaran::whereIn('id_anggaran', $ids)
            ->where('id_user', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted budgets deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No budgets found or authorized to delete.'], 404);
    }
}
