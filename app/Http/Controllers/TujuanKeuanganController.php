<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TujuanKeuangan;
use App\Models\TujuanKeuanganLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class TujuanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            $query = TujuanKeuangan::where('id_user', $userId);

            if ($request->filled('filter_kategori')) {
                $query->where('kategori', $request->filter_kategori);
            }

            if ($request->filled('filter_prioritas')) {
                $query->where('prioritas', $request->filter_prioritas);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('progress', function ($goal) {
                    $percentage = $goal->nominal_target > 0 ? ($goal->nominal_terkumpul / $goal->nominal_target) * 100 : 0;
                    return round(min($percentage, 100), 1);
                })
                ->addColumn('sisa_waktu', function ($goal) {
                    $deadline = Carbon::parse($goal->tenggat_waktu);
                    $now = Carbon::now();
                    if ($now->greaterThan($deadline)) {
                        return 'Expired';
                    }
                    $diff = $now->diff($deadline);
                    if ($diff->y > 0)
                        return $diff->y . ' year(s) ' . $diff->m . ' month(s)';
                    if ($diff->m > 0)
                        return $diff->m . ' month(s) ' . $diff->d . ' day(s)';
                    return $diff->d . ' day(s)';
                })
                ->addColumn('rekomendasi', function ($goal) {
                    $deadline = Carbon::parse($goal->tenggat_waktu);
                    $now = Carbon::now();
                    $months = $now->diffInMonths($deadline);

                    if ($goal->nominal_terkumpul >= $goal->nominal_target) {
                        return 0;
                    }

                    if ($months <= 0) {
                        $days = $now->diffInDays($deadline);
                        if ($days <= 0)
                            return $goal->nominal_target - $goal->nominal_terkumpul; // Needs to be fulfilled today
                        return ($goal->nominal_target - $goal->nominal_terkumpul) / max($days, 1); // per day if less than a month
                    }

                    return ($goal->nominal_target - $goal->nominal_terkumpul) / $months;
                })
                ->addColumn('aksi', function ($goal) {
                    return view('tujuan_keuangan.tombol')->with('goal', $goal);
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('tujuan_keuangan.index');
    }

    public function store(Request $request)
    {
        // Sanitize nominal input from dots (thousands separator)
        if ($request->has('nominal_target') && is_string($request->nominal_target)) {
            $request->merge([
                'nominal_target' => str_replace('.', '', $request->nominal_target)
            ]);
        }

        $validated = $request->validate([
            'nama_target' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'nominal_target' => 'required|numeric|min:0',
            'tenggat_waktu' => 'required|date|after_or_equal:today',
            'prioritas' => 'required|in:High,Medium,Low',
        ]);

        $validated['id_user'] = Auth::id();
        $validated['nominal_terkumpul'] = 0;

        TujuanKeuangan::create($validated);

        return redirect()->back()->with('success', 'Goal successfully created!');
    }

    public function update(Request $request, $id)
    {
        $goal = TujuanKeuangan::where('id_tujuan_keuangan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'nama_target' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'nominal_target' => 'required|numeric|min:0',
            'tenggat_waktu' => 'required|date',
            'prioritas' => 'required|in:High,Medium,Low',
            'nominal_terkumpul' => 'nullable|numeric|min:0'
        ]);

        // Remove null values to avoid database errors on non-nullable columns
        $validated = array_filter($validated, fn($value) => !is_null($value));

        $goal->update($validated);

        return redirect()->back()->with('success', 'Goal successfully updated!');
    }

    public function destroy($id)
    {
        $goal = TujuanKeuangan::where('id_tujuan_keuangan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $goal->delete();

        return response()->json(['success' => true]);
    }

    public function updateProgress(Request $request, $id)
    {
        $goal = TujuanKeuangan::where('id_tujuan_keuangan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Sanitize nominal input from dots (thousands separator)
        if ($request->has('nominal_tambah') && is_string($request->nominal_tambah)) {
            $request->merge([
                'nominal_tambah' => str_replace('.', '', $request->nominal_tambah)
            ]);
        }

        $validated = $request->validate([
            'nominal_tambah' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($goal, $validated) {
            $goal->nominal_terkumpul += $validated['nominal_tambah'];
            $goal->save();

            TujuanKeuanganLog::create([
                'id_tujuan_keuangan' => $goal->id_tujuan_keuangan,
                'nominal_tambah' => $validated['nominal_tambah'],
                'keterangan' => $validated['keterangan'] ?? 'Manual addition'
            ]);
        });

        return redirect()->back()->with('success', 'Progress updated!');
    }

    public function getHistory(Request $request, $id)
    {
        $goal = TujuanKeuangan::where('id_tujuan_keuangan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $logs = TujuanKeuanganLog::where('id_tujuan_keuangan', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function destroyLog($id)
    {
        $log = TujuanKeuanganLog::findOrFail($id);
        $goal = $log->goal;

        if ($goal->id_user !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($log, $goal) {
            $goal->nominal_terkumpul -= $log->nominal_tambah;
            $goal->save();
            $log->delete();
        });

        return response()->json(['success' => true]);
    }
}
