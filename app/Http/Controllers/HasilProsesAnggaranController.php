<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilProsesAnggaran;
use App\Models\Transaksi;


class HasilProsesAnggaranController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = HasilProsesAnggaran::all();
        return view('hasil_proses_anggaran.index', compact('data'));
    }

    // Tampilkan form tambah data
    public function create()
    {
        return view('hasil_proses_anggaran.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_proses_anggaran' => 'required|string|unique:hasil_proses_anggaran,id_proses_anggaran',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nama_anggaran' => 'required|string',
            'jenis_pengeluaran' => 'required|array',
            'persentase_anggaran' => 'required|numeric',
            'nominal_anggaran' => 'required|numeric',
            'anggaran_yang_digunakan' => 'required|numeric',
            'sisa_anggaran' => 'required|numeric',
        ]);

        HasilProsesAnggaran::create($validated);

        return redirect()->route('hasil_proses_anggaran.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    // Tampilkan detail data
    public function show($id)
    {
        $data = HasilProsesAnggaran::findOrFail($id);
        return view('hasil_proses_anggaran.show', compact('data'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $data = HasilProsesAnggaran::findOrFail($id);
        return view('hasil_proses_anggaran.edit', compact('data'));
    }

    // Proses update data
    public function update(Request $request, $id)
    {
        $data = HasilProsesAnggaran::findOrFail($id);

        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nama_anggaran' => 'required|string',
            'jenis_pengeluaran' => 'required|array',
            'persentase_anggaran' => 'required|numeric',
            'nominal_anggaran' => 'required|numeric',
            'anggaran_yang_digunakan' => 'required|numeric',
            'sisa_anggaran' => 'required|numeric',
        ]);

        $data->update($validated);

        // Ambil total transaksi yang cocok dengan jenis_pengeluaran dan dalam rentang tanggal
        $totalPengeluaran = Transaksi::whereIn('id_pengeluaran', $validated['jenis_pengeluaran'])
            ->whereBetween('tanggal_transaksi', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
            ->sum('nominal');

        // Hitung sisa anggaran berdasarkan transaksi
        $sisa = $validated['nominal_anggaran'] - $totalPengeluaran;

        // Update kembali anggaran_yang_digunakan dan sisa_anggaran
        $data->update([
            'anggaran_yang_digunakan' => $totalPengeluaran,
            'sisa_anggaran' => $sisa
        ]);

        return redirect()->route('hasil_proses_anggaran.index')
            ->with('success', 'Data updated successfully.');
    }

    // Hapus data
    public function destroy($id)
    {
        $data = HasilProsesAnggaran::findOrFail($id);
        $data->delete();

        return redirect()->route('hasil_proses_anggaran.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
