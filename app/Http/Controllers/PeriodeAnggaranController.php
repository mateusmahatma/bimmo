<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\PeriodeAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PeriodeAnggaranController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = PeriodeAnggaran::where('id_user', $userId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_periode', 'like', "%{$search}%")
                    ->orWhere('tanggal_mulai', 'like', "%{$search}%")
                    ->orWhere('tanggal_selesai', 'like', "%{$search}%");
            });
        }

        $periods = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();

        $allPeriods = PeriodeAnggaran::where('id_user', $userId)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('anggaran.index', compact('periods', 'allPeriods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode' => ['required', 'string', 'min:3', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ], [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $validated['id_user'] = Auth::id();

        $periode = PeriodeAnggaran::create($validated);

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json([
                'success' => true,
                'message' => 'Periode anggaran berhasil dibuat.',
                'id' => $periode->id_periode_anggaran,
            ]);
        }

        return redirect()->route('anggaran.index')->with('success', 'Periode anggaran berhasil dibuat.');
    }

    public function copy(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'nama_periode_copy' => ['required', 'string', 'min:3', 'max:255'],
            'tanggal_mulai_copy' => ['required', 'date'],
            'tanggal_selesai_copy' => ['required', 'date', 'after_or_equal:tanggal_mulai_copy'],
            'source_periode_id' => [
                'required',
                'integer',
                Rule::exists('periode_anggaran', 'id_periode_anggaran')->where(function ($query) use ($userId) {
                    $query->where('id_user', $userId);
                }),
            ],
        ], [
            'nama_periode_copy.required' => 'Nama periode wajib diisi.',
            'tanggal_mulai_copy.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai_copy.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai_copy.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'source_periode_id.required' => 'Periode sumber wajib dipilih.',
            'source_periode_id.exists' => 'Periode sumber tidak ditemukan.',
        ]);

        $source = PeriodeAnggaran::where('id_user', $userId)
            ->where('id_periode_anggaran', $validated['source_periode_id'])
            ->firstOrFail();

        $newPeriode = null;

        DB::transaction(function () use ($userId, $validated, $source, &$newPeriode) {
            $newPeriode = PeriodeAnggaran::create([
                'id_user' => $userId,
                'nama_periode' => $validated['nama_periode_copy'],
                'tanggal_mulai' => $validated['tanggal_mulai_copy'],
                'tanggal_selesai' => $validated['tanggal_selesai_copy'],
            ]);

            $items = Anggaran::where('id_user', $userId)
                ->where('id_periode_anggaran', $source->id_periode_anggaran)
                ->get();

            foreach ($items as $item) {
                Anggaran::create([
                    'id_user' => $userId,
                    'id_periode_anggaran' => $newPeriode->id_periode_anggaran,
                    'nama_anggaran' => $item->nama_anggaran,
                    'persentase_anggaran' => $item->persentase_anggaran,
                    'id_pengeluaran' => $item->id_pengeluaran,
                ]);
            }
        });

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json([
                'success' => true,
                'message' => 'Periode anggaran berhasil dicopy.',
                'id' => $newPeriode?->id_periode_anggaran,
            ]);
        }

        return redirect()->route('anggaran.index')->with('success', 'Periode anggaran berhasil dicopy.');
    }

    public function destroy(Request $request, PeriodeAnggaran $periode)
    {
        if ($periode->id_user !== Auth::id()) {
            abort(403);
        }

        $periode->delete();

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json(['success' => true, 'message' => 'Periode anggaran berhasil dihapus.']);
        }

        return redirect()->route('anggaran.index')->with('success', 'Periode anggaran berhasil dihapus.');
    }
}
