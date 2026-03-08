<?php

namespace App\Imports;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AsetImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_aset'] ?? null;
            if ($name) {
                // Check if already exists for this user to avoid duplicates
                $exists = Aset::where('id_user', Auth::id())
                    ->where('nama_aset', $name)
                    ->exists();

                if (!$exists) {
                    $tanggal = $row['tanggal_pembelian_yyyy_mm_dd'] ?? Carbon::now()->format('Y-m-d');

                    Aset::create([
                        'id_user' => Auth::id(),
                        'nama_aset' => $name,
                        'kategori' => $row['kategori'] ?? 'Lainnya',
                        'merk_model' => $row['merk_model'] ?? '-',
                        'tanggal_pembelian' => $tanggal,
                        'harga_beli' => $row['harga_beli'] ?? 0,
                        'masa_pakai' => $row['masa_pakai_tahun'] ?? 1,
                        'nilai_sisa' => $row['nilai_sisa'] ?? 0,
                        'kondisi' => 'Baik',
                    ]);
                }
            }
        }
    }
}
