<?php

namespace App\Imports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class PengeluaranCategoryImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_kategori_pengeluaran'] ?? null;
            if ($name) {
                // Check if already exists for this user to avoid duplicates
                $exists = Pengeluaran::where('id_user', Auth::id())
                    ->where('nama', $name)
                    ->exists();

                if (!$exists) {
                    Pengeluaran::create([
                        'nama' => $name,
                        'id_user' => Auth::id()
                    ]);
                }
            }
        }
    }
}
