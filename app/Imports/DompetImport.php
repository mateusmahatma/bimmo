<?php

namespace App\Imports;

use App\Models\Dompet;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class DompetImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_dompet'] ?? null;
            $balance = $row['saldo_awal'] ?? 0;

            if ($name) {
                // Check if already exists for this user to avoid duplicates
                $exists = Dompet::where('id_user', Auth::id())
                    ->where('nama', $name)
                    ->exists();

                if (!$exists) {
                    Dompet::create([
                        'nama' => $name,
                        'saldo' => $balance,
                        'id_user' => Auth::id(),
                        'ikon' => 'bi-wallet2', // Default icon
                        'status' => 1 // Default active
                    ]);
                }
            }
        }
    }
}
