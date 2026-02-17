<?php

namespace App\Imports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiImportTest implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
    // Logic dipindah ke Controller untuk handle multiple sheets
    }
}
