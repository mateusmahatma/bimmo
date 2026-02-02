<?php

namespace App\Imports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiImportTest implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $data = [];

        foreach ($rows as $row) {

            // ⛔ skip baris kosong
            if (empty($row['tgl_transaksi'])) {
                continue;
            }

            // 🗓️ konversi tanggal
            try {
                if (is_numeric($row['tgl_transaksi'])) {
                    $tgl = Carbon::instance(
                        Date::excelToDateTimeObject($row['tgl_transaksi'])
                    )->format('Y-m-d');
                } else {
                    $tgl = Carbon::parse($row['tgl_transaksi'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }

            $data[] = [
                'tgl_transaksi'     => $tgl,
                'pemasukan'         => $row['pemasukan'] ?? null,
                'nominal_pemasukan' => $row['nominal_pemasukan'] ?? 0,
                'pengeluaran'       => $row['pengeluaran'] ?? null,
                'nominal'           => $row['nominal'] ?? 0,
                'keterangan'        => $row['keterangan'] ?? null,
                'status'            => $row['status'] ?? 1,
                'id_user'           => Auth::id(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // ⛔ kalau kosong, STOP
        if (count($data) === 0) {
            throw new \Exception('Tidak ada data valid untuk diimport');
        }

        // ✅ INSERT SEKALI (PASTI MASUK)
        Transaksi::insert($data);
    }
}
