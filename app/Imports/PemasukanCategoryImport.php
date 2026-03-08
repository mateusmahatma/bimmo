<?php

namespace App\Imports;

use App\Models\Pemasukan;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class PemasukanCategoryImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_kategori_pemasukan'] ?? null;
            if ($name) {
                // Check if already exists for this user to avoid duplicates
                $exists = Pemasukan::where('id_user', Auth::id())
                    ->where('nama', $name)
                    ->exists();

                if (!$exists) {
                    Pemasukan::create([
                        'nama' => $name,
                        'id_user' => Auth::id()
                    ]);
                }
            }
        }
    }
}
