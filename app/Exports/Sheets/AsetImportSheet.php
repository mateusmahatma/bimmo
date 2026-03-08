<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AsetImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Laptop MacBook Air', 'Elektronik', 'Apple M2', '2023-01-15', '18000000', '5', '2000000'],
            ['Motor Beat', 'Kendaraan', 'Honda 2022', '2022-05-10', '17000000', '8', '3000000'],
            ['Meja Kerja', 'Furniture', 'Informa L-Shape', '2023-06-20', '1500000', '10', '100000'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Aset',
            'Kategori',
            'Merk/Model',
            'Tanggal Pembelian (YYYY-MM-DD)',
            'Harga Beli',
            'Masa Pakai (Tahun)',
            'Nilai Sisa'
        ];
    }

    public function title(): string
    {
        return 'Template Import Aset';
    }
}
