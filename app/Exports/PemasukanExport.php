<?php

namespace App\Exports;

use App\Models\Pemasukan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithTitle;



class PemasukanExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Pemasukan::where('id_user', Auth::id())
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
        return 'Pemasukan';
    }
}
