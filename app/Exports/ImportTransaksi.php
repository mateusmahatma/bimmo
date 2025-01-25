<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ImportTransaksi implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'Tanggal Transaksi',
            'Jenis Pemasukan',
            'Nominal Pemasukan',
            'Jenis Pengeluaran',
            'Nominal Pengeluaran',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        return [
            $row->tgl_transaksi,
            $row->pemasukan,
            $row->nominal_pemasukan,
            $row->pengeluaran,
            $row->nominal,
            $row->keterangan
        ];
    }

    public function title(): string
    {
        return 'Template Import Transaksi';
    }
}
