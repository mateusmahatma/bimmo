<?php

namespace App\Imports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Auth;

class TransaksiImport implements ToModel, WithHeadingRow
{
    public function sheets(): array
    {
        // Tentukan sheet yang ingin diambil, misalnya sheet 'Transaksi'
        return [
            'Template Import Transaksi' => new TransaksiImport(),
        ];
    }

    public function model(array $row)
    {
        // Validasi apakah baris kosong atau tidak memiliki data penting
        if (
            empty($row['tanggal_transaksi']) &&
            empty($row['jenis_pemasukan']) &&
            empty($row['jenis_pengeluaran']) &&
            empty($row['keterangan'])
        ) {
            // Abaikan baris kosong atau tidak valid
            return null;
        }

        // Inisialisasi tanggal transaksi
        $tanggalTransaksi = null;
        if (!empty($row['tanggal_transaksi']) && is_numeric($row['tanggal_transaksi'])) {
            $tanggalTransaksi = Date::excelToDateTimeObject($row['tanggal_transaksi'])->format('Y-m-d');
        }

        // Return model baru hanya untuk baris yang valid
        return new Transaksi([
            'tgl_transaksi' => $tanggalTransaksi,
            'pemasukan' => !empty($row['jenis_pemasukan']) ? $row['jenis_pemasukan'] : null,
            'nominal_pemasukan' => !empty($row['nominal_pemasukan']) ? $row['nominal_pemasukan'] : null,
            'pengeluaran' => !empty($row['jenis_pengeluaran']) ? $row['jenis_pengeluaran'] : null,
            'nominal' => !empty($row['nominal_pengeluaran']) ? $row['nominal_pengeluaran'] : null,
            'keterangan' => !empty($row['keterangan']) ? $row['keterangan'] : null,
            'id_user' => Auth::id(),
        ]);
    }
}
