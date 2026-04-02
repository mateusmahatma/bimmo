<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PinjamanExport;

class PinjamanController extends Controller
{
    public function exportExcel(Request $request)
    {
        $filterStatus = $request->input('filter_status');
        return Excel::download(new PinjamanExport($filterStatus), 'pinjaman.xlsx');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pinjaman::where('id_user', $userId)->with('bayar_pinjaman');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_pinjaman', 'LIKE', "%{$search}%");
        }

        // Filter Status
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        // Summary calculations (before pagination but after filter)
        // Note: sum counts all rows in the query
        $totalRemaining = (clone $query)->sum('jumlah_pinjaman');
        $ids = (clone $query)->pluck('id');
        $totalPaid = \App\Models\BayarPinjaman::whereIn('id_pinjaman', $ids)->sum('jumlah_bayar');
        $totalOriginal = $totalRemaining + $totalPaid;
        $totalNextMonthInstallment = (clone $query)->where('status', 'belum_lunas')->get()->sum(function ($p) {
            return min($p->nominal_angsuran, $p->jumlah_pinjaman);
        });

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        // Allowed sort columns
        $allowedSort = ['nama_pinjaman', 'jumlah_pinjaman', 'created_at', 'status'];
        if (in_array($sort, $allowedSort)) {
            $query->orderBy($sort, $direction);
        }
        else {
            $query->orderBy('created_at', 'desc');
        }

        $pinjaman = $query->paginate(10)->withQueryString();

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json([
                'html' => view('pinjaman._table_list', compact('pinjaman'))->render(),
                'totalPinjaman' => 'Rp ' . number_format($totalRemaining, 0, ',', '.'),
                'totalPaid' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
                'totalOriginal' => 'Rp ' . number_format($totalOriginal, 0, ',', '.'),
                'totalNextMonthInstallment' => 'Rp ' . number_format($totalNextMonthInstallment, 0, ',', '.'),
            ]);
        }

        return view('pinjaman.index', compact('pinjaman', 'totalRemaining', 'totalPaid', 'totalOriginal', 'totalNextMonthInstallment'));
    }

    public function create()
    {
        return view('pinjaman.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'numeric',
            'nominal_angsuran' => 'nullable|numeric',
            'jangka_waktu' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:lunas,belum_lunas',
            'keterangan' => 'nullable|string',
            'simulasi_cicilan' => 'nullable|string',
        ]);

        $validatedData['id_user'] = Auth::id();

        if (isset($validatedData['simulasi_cicilan'])) {
            $validatedData['simulasi_cicilan'] = json_decode($validatedData['simulasi_cicilan'], true);
        }

        Pinjaman::create($validatedData);

        return redirect()->route('pinjaman.index')->with('success', 'Pinjaman Berhasil Tersimpan.');
    }

    public function show($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $pinjaman = Pinjaman::with('user', 'bayar_pinjaman')->findOrFail($id);
        return view('pinjaman.show', compact('pinjaman'));
    }

    public function edit($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $data = Pinjaman::where('id', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $validasi = Validator::make($request->all(), [
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'numeric',
            'nominal_angsuran' => 'nullable|numeric',
            'jangka_waktu' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:lunas,belum_lunas',
            'keterangan' => 'nullable|string',
            'simulasi_cicilan' => 'nullable|string',
        ], [
            'nama_pinjaman.required' => 'Nama wajib diisi',
        ]);

        if ($validasi->fails()) {
            return response()->json(['errors' => $validasi->errors()], 422);
        }

        $data = [
            'nama_pinjaman' => $request->nama_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'nominal_angsuran' => $request->nominal_angsuran,
            'jangka_waktu' => $request->jangka_waktu,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ];

        if ($request->has('simulasi_cicilan')) {
            $data['simulasi_cicilan'] = json_decode($request->simulasi_cicilan, true);
        }

        Pinjaman::where('id', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }



    public function destroy($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $pinjaman = Pinjaman::findOrFail($id);
        $pinjaman->delete(); // Ini akan otomatis menghapus semua pembayaran terkait

        return response()->json([
            'success' => true,
            'message' => 'Pinjaman dan semua pembayaran terkait telah dihapus.'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
        ]);

        $hashedIds = $validated['ids'];
        $ids = [];
        foreach ($hashedIds as $hash) {
            $decoded = Hashids::decode($hash)[0] ?? null;
            if ($decoded) {
                $ids[] = $decoded;
            }
        }

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Invalid IDs.'], 400);
        }

        // Ensure user owns these records
        $deleted = Pinjaman::whereIn('id', $ids)
            ->where('id_user', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted loans deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No loans found or authorized to delete.'], 404);
    }
}
