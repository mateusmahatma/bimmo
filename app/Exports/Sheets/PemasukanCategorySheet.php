<?php

namespace App\Exports\Sheets;

use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PemasukanCategorySheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Pemasukan::where('id_user', Auth::id())
            ->get(['id', 'nama']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Kategori'
        ];
    }

    public function title(): string
    {
        return 'Referensi Kategori';
    }
}
