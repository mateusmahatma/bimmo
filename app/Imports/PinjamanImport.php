<?php

namespace App\Imports;

use App\Models\Pinjaman;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PinjamanImport implements ToArray, WithHeadingRow
{
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $name = $row['nama_pinjaman_piutang'] ?? null;
            if ($name) {
                // Check if already exists for this user
                $exists = Pinjaman::where('id_user', Auth::id())
                    ->where('nama_pinjaman', $name)
                    ->exists();

                if (!$exists) {
                    $jumlah = $row['jumlah_pinjaman'] ?? 0;
                    $tenor = $row['jangka_waktu_bulan_isi_0_jika_tidak_ada'] ?? 0;
                    $startDate = $row['tanggal_mulai_yyyy_mm_dd'] ?? Carbon::now()->format('Y-m-d');

                    $endDate = Carbon::parse($startDate)->addYears(100)->format('Y-m-d'); // Fallback for open-ended
                    if ($tenor > 0) {
                        $endDate = Carbon::parse($startDate)->addMonths($tenor)->format('Y-m-d');
                    }

                    Pinjaman::create([
                        'id_user' => Auth::id(),
                        'nama_pinjaman' => $name,
                        'jumlah_pinjaman' => $jumlah,
                        'nominal_awal' => $jumlah,
                        'nominal_sisa' => $jumlah,
                        'jumlah_angsuran' => $tenor,
                        'angsuran_ke' => 0,
                        'sisa_angsuran' => $tenor,
                        'jangka_waktu' => $tenor,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'keterangan' => $row['keterangan'] ?? '-',
                        'status' => 'belum_lunas'
                    ]);
                }
            }
        }
    }
}
