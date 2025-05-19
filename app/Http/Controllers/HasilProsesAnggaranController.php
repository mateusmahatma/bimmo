<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilProsesAnggaran;


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

        return redirect()->route('hasil_proses_anggaran.index')
            ->with('success', 'Data berhasil diperbarui.');
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
