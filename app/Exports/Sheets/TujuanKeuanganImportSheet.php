<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TujuanKeuanganImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Beli Rumah', 'Properti', '500000000', '50000000', '2030-12-31', 'High'],
            ['Liburan ke Jepang', 'Hiburan', '30000000', '5000000', '2025-06-15', 'Medium'],
            ['Dana Darurat', 'Dana Simpanan', '50000000', '10000000', '2024-12-31', 'High'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Target',
            'Kategori',
            'Nominal Target',
            'Nominal Terkumpul',
            'Tenggat Waktu (YYYY-MM-DD)',
            'Prioritas (High/Medium/Low)'
        ];
    }

    public function title(): string
    {
        return 'Template Import Tujuan Keuangan';
    }
}
