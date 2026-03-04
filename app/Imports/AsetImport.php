<?php

namespace App\Imports;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AsetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Simple mapping based on expected headers
        return new Aset([
            'id_user' => Auth::id(),
            'kode_aset' => $row['kode_aset'] ?? 'AST-' . time() . rand(10, 99),
            'nama_aset' => $row['nama_aset'],
            'kategori' => $row['kategori'],
            'merk_model' => $row['merk_model'] ?? null,
            'nomor_seri' => $row['nomor_seri'] ?? null,
            'tanggal_pembelian' => isset($row['tanggal_pembelian']) ?Carbon::parse($row['tanggal_pembelian']) : now(),
            'harga_beli' => $row['harga_beli'] ?? 0,
            'masa_pakai' => $row['masa_pakai'] ?? 5,
            'nilai_sisa' => $row['nilai_sisa'] ?? 0,
            'kondisi' => $row['kondisi'] ?? 'Baik',
            'lokasi' => $row['lokasi'] ?? null,
            'pic' => $row['pic'] ?? null,
        ]);
    }
}
