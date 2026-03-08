<?php

namespace App\Exports\Sheets;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\Auth;

class AnggaranReferenceSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Pengeluaran::where('id_user', Auth::id())
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Daftar Kategori Pengeluaran Tersedia'
        ];
    }

    public function title(): string
    {
        return 'Referensi Kategori';
    }
}
