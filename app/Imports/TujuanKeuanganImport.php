<?php

namespace App\Imports;

use App\Models\TujuanKeuangan;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TujuanKeuanganImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_target'] ?? null;
            if ($name) {
                // Check if already exists for this user
                $exists = TujuanKeuangan::where('id_user', Auth::id())
                    ->where('nama_target', $name)
                    ->exists();

                if (!$exists) {
                    $deadline = $row['tenggat_waktu_yyyy_mm_dd'] ?? Carbon::now()->addYear()->format('Y-m-d');

                    TujuanKeuangan::create([
                        'id_user' => Auth::id(),
                        'nama_target' => $name,
                        'kategori' => $row['kategori'] ?? 'Impian',
                        'nominal_target' => $row['nominal_target'] ?? 0,
                        'nominal_terkumpul' => $row['nominal_terkumpul'] ?? 0,
                        'tenggat_waktu' => $deadline,
                        'prioritas' => $row['prioritas_highmediumlow'] ?? 'Medium',
                    ]);
                }
            }
        }
    }
}
