<?php

namespace App\Imports;

use App\Models\Anggaran;
use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class AnggaranImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_anggaran'] ?? null;
            if ($name) {
                // Check if already exists for this user
                $exists = Anggaran::where('id_user', Auth::id())
                    ->where('nama_anggaran', $name)
                    ->exists();

                if (!$exists) {
                    $categoriesText = $row['kategori_pengeluaran'] ?? '';
                    $categoryNames = array_filter(array_map('trim', explode(',', $categoriesText)));

                    $userId = Auth::id();
                    $categoryIds = [];
                    if (!empty($categoryNames)) {
                        $categoryIds = Pengeluaran::where('id_user', $userId)
                            ->whereIn('nama', $categoryNames)
                            ->pluck('id')
                            ->map(fn($id) => (string)$id)
                            ->toArray();
                    }

                    Anggaran::create([
                        'id_user' => $userId,
                        'nama_anggaran' => $name,
                        'persentase_anggaran' => $row['persentase_anggaran'] ?? 0,
                        'id_pengeluaran' => !empty($categoryIds) ? $categoryIds : null,
                    ]);
                }
            }
        }
    }
}
