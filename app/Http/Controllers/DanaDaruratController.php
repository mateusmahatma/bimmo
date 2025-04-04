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

            // Ambil data sesuai user yang login
            $query = DanaDarurat::where('id_user', $userId);

            // Hitung total dana darurat
            $totalDanaDarurat = $query->sum('nominal_dana_darurat');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($dana) {
                    // return view('dana_darurat.tombol')->with('request', $dana);
                })
                ->with('totalDanaDarurat', 'Rp ' . number_format($totalDanaDarurat, 0, ',', '.')) // Kirim total dana darurat ke frontend
                ->toJson();
        } else {
            return view('dana_darurat.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
