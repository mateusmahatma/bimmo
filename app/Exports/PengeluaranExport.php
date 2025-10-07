<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithTitle;

class PengeluaranExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Pengeluaran::where('id_user', Auth::id())
            ->get(['id', 'nama']);
    }

    public function headings(): array
    {
        return [
            'id',
            'nama'
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran';
    }
}
