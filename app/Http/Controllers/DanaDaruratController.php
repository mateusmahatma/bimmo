<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DanaDarurat;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DanaDaruratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();

            // Ambil semua data transaksi untuk user
            $query = DanaDarurat::where('id_user', $userId);

            // Hitung total dana darurat: total masuk - total keluar
            $totalMasuk = DanaDarurat::where('id_user', $userId)
                ->where('jenis_transaksi_dana_darurat', 1)
                ->sum('nominal_dana_darurat');

            $totalKeluar = DanaDarurat::where('id_user', $userId)
                ->where('jenis_transaksi_dana_darurat', 2)
                ->sum('nominal_dana_darurat');

            $totalDanaDarurat = $totalMasuk - $totalKeluar;

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('jenis_transaksi_dana_darurat', function ($dana) {
                return $dana->jenis_transaksi_dana_darurat == 1 ? 'Masuk' : 'Keluar';
            })
                ->addColumn('aksi', function ($dana) {
                return view('dana_darurat.tombol')->with('request', $dana);
            })
                ->with('totalDanaDarurat', $totalDanaDarurat)
                ->toJson();
        }
        else {
            return view('dana_darurat.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dana = new DanaDarurat();
        $dana->id_user = Auth::id();

        return view('dana_darurat.create', compact('dana'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tgl_transaksi_dana_darurat' => 'required|date',
            'jenis_transaksi_dana_darurat' => 'required|in:1,2',
            'nominal_dana_darurat' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $validatedData['id_user'] = Auth::id();

        DanaDarurat::create($validatedData);
        return redirect('/dana-darurat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $dana = DanaDarurat::where('id_dana_darurat', $id)->first();

        return view('dana_darurat.edit', compact('dana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'tgl_transaksi_dana_darurat' => 'required|date',
            'jenis_transaksi_dana_darurat' => 'required|in:1,2',
            'nominal_dana_darurat' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);

        DanaDarurat::where('id_dana_darurat', $id)->update($validatedData);

        return redirect()->route('dana-darurat.index')
            ->with('success', 'Berhasil update Dana Darurat!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dana = DanaDarurat::findOrFail($id);
        $dana->delete();
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:dana_darurat,id_dana_darurat'
        ]);

        $ids = $validated['ids'];

        // Ensure user owns these records
        $deleted = DanaDarurat::whereIn('id_dana_darurat', $ids)
            ->where('id_user', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted data deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No data found or authorized to delete.'], 404);
    }
}
