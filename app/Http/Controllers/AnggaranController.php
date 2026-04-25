<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggaran;
use App\Models\Pengeluaran;
use App\Models\PeriodeAnggaran;

class AnggaranController extends Controller
{
    private function scopedQuery(int $userId, ?int $periodeId)
    {
        $query = Anggaran::where('id_user', $userId);

        if ($periodeId === null) {
            $query->whereNull('id_periode_anggaran');
        } else {
            $query->where('id_periode_anggaran', $periodeId);
        }

        return $query;
    }

    private function renderIndex(Request $request, PeriodeAnggaran $periode)
    {
        $userId = Auth::id();
        $periodeId = $periode->id_periode_anggaran;
        $query = $this->scopedQuery($userId, $periodeId);

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

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
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

        $baseUrl = route('anggaran.detail', $periode->id_periode_anggaran);

        return view('anggaran.detail', compact('anggarans', 'anggaran', 'pengeluarans', 'totalPersentase', 'exceedMessage', 'sort', 'direction', 'periode', 'baseUrl'));
    }

    private function storeScoped(Request $request, PeriodeAnggaran $periode)
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
        $periodeId = $periode?->id_periode_anggaran;
        $currentTotal = $this->scopedQuery($userId, $periodeId)->sum('persentase_anggaran');
        $newTotal = $currentTotal + $request->persentase_anggaran;

        // Validasi total melebihi 100%
        if ($newTotal > 100) {
            if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
                return response()->json(['errors' => ['persentase_anggaran' => ['Persentase anggaran sudah melebihi 100% mohon dicek kembali.']]], 422);
            }
            return back()
                ->withErrors([
                'persentase_anggaran' => 'Persentase anggaran sudah melebihi 100% mohon dicek kembali.',
            ])
                ->withInput();
        }

        $validatedData['id_user'] = $userId;
        $validatedData['id_periode_anggaran'] = $periodeId;
        Anggaran::create($validatedData);

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);
        }

        return redirect()->route('anggaran.detail', $periode->id_periode_anggaran)->with('success', 'Data berhasil disimpan!');
    }

    private function editScoped(Request $request, int $id, PeriodeAnggaran $periode)
    {
        $userId = Auth::id();
        $periodeId = $periode->id_periode_anggaran;

        $anggaran = $this->scopedQuery($userId, $periodeId)
            ->where('id_anggaran', $id)
            ->firstOrFail();

        // Decode id_pengeluaran agar menjadi array
        $selectedIds = is_string($anggaran->id_pengeluaran)
            ? json_decode($anggaran->id_pengeluaran, true)
            : ($anggaran->id_pengeluaran ?? []);

        // Tampilkan semua pengeluaran user agar bisa diubah
        $pengeluarans = Pengeluaran::where('id_user', $userId)->get();

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json(['result' => $anggaran]);
        }

        return view('anggaran.edit', compact('anggaran', 'pengeluarans', 'selectedIds'));
    }

    private function updateScoped(Request $request, int $id, PeriodeAnggaran $periode)
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

        $periodeId = $periode->id_periode_anggaran;

        $anggaran = $this->scopedQuery($userId, $periodeId)
            ->where('id_anggaran', $id)
            ->firstOrFail();

        $totalPersenTerpakai = $this->scopedQuery($userId, $periodeId)
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

        return redirect()->route('anggaran.detail', $periode->id_periode_anggaran)->with('success', 'Berhasil update anggaran!');
    }

    private function destroyScoped(Request $request, int $id, PeriodeAnggaran $periode)
    {
        $userId = Auth::id();
        $periodeId = $periode->id_periode_anggaran;

        $deleted = $this->scopedQuery($userId, $periodeId)
            ->where('id_anggaran', $id)
            ->delete();

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Data deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Data not found or unauthorized'], 404);
        }
        return redirect()->back();
    }

    private function bulkDeleteScoped(Request $request, PeriodeAnggaran $periode)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:anggaran,id_anggaran' // Adjusted to id_anggaran based on destroy method usage
        ]);

        $ids = $validated['ids'];

        // Ensure user owns these records
        $userId = Auth::id();
        $periodeId = $periode->id_periode_anggaran;

        $deleted = $this->scopedQuery($userId, $periodeId)
            ->whereIn('id_anggaran', $ids)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted budgets deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No budgets found or authorized to delete.'], 404);
    }

    // ----- Routes: Periode -----
    public function periodeIndex(Request $request, PeriodeAnggaran $periode)
    {
        if ($periode->id_user !== Auth::id()) abort(403);
        return $this->renderIndex($request, $periode);
    }

    public function periodeStore(Request $request, PeriodeAnggaran $periode)
    {
        if ($periode->id_user !== Auth::id()) abort(403);
        return $this->storeScoped($request, $periode);
    }

    public function periodeEdit(Request $request, PeriodeAnggaran $periode, $id)
    {
        if ($periode->id_user !== Auth::id()) abort(403);
        return $this->editScoped($request, (int) $id, $periode);
    }

    public function periodeUpdate(Request $request, PeriodeAnggaran $periode, $id)
    {
        if ($periode->id_user !== Auth::id()) abort(403);
        return $this->updateScoped($request, (int) $id, $periode);
    }

    public function periodeDestroy(Request $request, PeriodeAnggaran $periode, $id)
    {
        if ($periode->id_user !== Auth::id()) abort(403);
        return $this->destroyScoped($request, (int) $id, $periode);
    }

    public function periodeBulkDelete(Request $request, PeriodeAnggaran $periode)
    {
        if ($periode->id_user !== Auth::id()) abort(403);
        return $this->bulkDeleteScoped($request, $periode);
    }
}
