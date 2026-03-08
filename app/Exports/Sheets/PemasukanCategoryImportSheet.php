<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PemasukanCategoryImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Gaji Utama'],
            ['Bonus Proyek'],
            ['Investasi'],
            ['Penjualan Barang'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Kategori Pemasukan'
        ];
    }

    public function title(): string
    {
        return 'Template Kategori Pemasukan';
    }
}
