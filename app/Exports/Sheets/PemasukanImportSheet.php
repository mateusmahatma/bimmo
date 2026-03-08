<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PemasukanImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            [
                '2024-03-01',
                'Gaji Harian',
                '500000',
                'Gaji lembur minggu pertama',
                '1'
            ],
            [
                '2024-03-05',
                'Bonus',
                '1000000',
                'Bonus performa bulanan',
                '2'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Tanggal Transaksi (YYYY-MM-DD)',
            'Nama Kategori Pemasukan',
            'Nominal Pemasukan',
            'Keterangan',
            'ID Dompet (Lihat sheet Referensi Dompet)'
        ];
    }

    public function title(): string
    {
        return 'Template Import Pemasukan';
    }
}
