<?php

namespace App\Exports\Sheets;

use App\Models\Dompet;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DompetReferenceSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Dompet::where('id_user', Auth::id())
            ->get(['id', 'nama', 'saldo']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Dompet',
            'Saldo Terakhir'
        ];
    }

    public function title(): string
    {
        return 'Referensi Dompet';
    }
}
