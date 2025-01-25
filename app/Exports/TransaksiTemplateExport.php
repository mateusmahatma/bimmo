<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransaksiTemplateExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new ImportTransaksi(),
            new PemasukanExport(),
            new PengeluaranExport(),
        ];
    }
}
