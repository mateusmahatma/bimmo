<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnggaranTemplateExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new Sheets\AnggaranImportSheet(),
            new Sheets\AnggaranReferenceSheet(),
        ];
    }
}
