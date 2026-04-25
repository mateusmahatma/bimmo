<?php

namespace App\Http\Controllers;

use App\Models\PeriodeAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('anggaran.index', compact('periods'));
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
