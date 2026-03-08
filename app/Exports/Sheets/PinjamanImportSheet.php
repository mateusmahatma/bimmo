<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PinjamanImportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            ['Kredit Usaha Rakyat', '25000000', '12', '2024-01-01', 'Pinjaman untuk modal usaha'],
            ['Hutang Teman - Andi', '500000', '0', '2024-02-15', 'Pinjaman tanpa tenor'],
            ['KPR Rumah', '500000000', '180', '2023-05-20', 'Cicilan rumah'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Pinjaman / Piutang',
            'Jumlah Pinjaman',
            'Jangka Waktu (Bulan - isi 0 jika tidak ada)',
            'Tanggal Mulai (YYYY-MM-DD)',
            'Keterangan'
        ];
    }

    public function title(): string
    {
        return 'Template Import Pinjaman';
    }
}
