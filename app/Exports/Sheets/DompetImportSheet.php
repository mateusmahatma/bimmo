<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DompetImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Dompet Utama', '5000000'],
            ['Tabungan Bank', '10000000'],
            ['E-Wallet', '250000'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Dompet',
            'Saldo Awal'
        ];
    }

    public function title(): string
    {
        return 'Template Import Dompet';
    }
}
