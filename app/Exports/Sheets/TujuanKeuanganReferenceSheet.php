<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TujuanKeuanganReferenceSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Kategori', 'Savings'],
            ['Kategori', 'Investment'],
            ['Kategori', 'Purchase'],
            ['Kategori', 'Debt'],
            ['Kategori', 'Others'],
            ['', ''],
            ['Prioritas', 'High'],
            ['Prioritas', 'Medium'],
            ['Prioritas', 'Low'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Tipe Referensi',
            'Nilai yang Diizinkan'
        ];
    }

    public function title(): string
    {
        return 'Referensi Kategori & Prioritas';
    }
}
