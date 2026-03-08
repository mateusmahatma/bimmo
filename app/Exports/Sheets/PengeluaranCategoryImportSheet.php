<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PengeluaranCategoryImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Makan & Minum'],
            ['Transportasi'],
            ['Belanja'],
            ['Kesehatan'],
            ['Hiburan'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Kategori Pengeluaran'
        ];
    }

    public function title(): string
    {
        return 'Template Kategori Pengeluaran';
    }
}
