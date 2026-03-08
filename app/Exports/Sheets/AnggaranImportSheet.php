<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AnggaranImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Kebutuhan Pokok', '50', 'Makan & Minum, Transportasi'],
            ['Hiburan & Lifestyle', '20', 'Hiburan'],
            ['Tabungan & Investasi', '30', 'Belanja'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Anggaran',
            'Persentase Anggaran (%)',
            'Kategori Pengeluaran'
        ];
    }

    public function title(): string
    {
        return 'Template Import Anggaran';
    }
}
