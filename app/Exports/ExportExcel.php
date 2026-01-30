<?php

// namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Illuminate\Support\Collection;
// use Carbon\Carbon;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// class ExportExcel implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
// {
//     protected $data;

//     public function __construct(Collection $data)
//     {
//         $this->data = $data;
//     }

//     public function collection()
//     {
//         $numberedData = $this->data->map(function ($item, $key) {
//             return [
//                 'No' => $key + 1,
//                 'Tanggal Transaksi' => Carbon::parse($item->tgl_transaksi)->format('Y-m-d'),
//                 'Pemasukan' => $item->pemasukanRelation?->nama ?? '-',
//                 'Nominal Pemasukan' => $item->nominal_pemasukan,
//                 'Pengeluaran' => $item->pengeluaranRelation?->nama ?? '-',
//                 'Nominal' => $item->nominal,
//                 'Keterangan' => $item->keterangan,
//             ];
//         });
//         return $numberedData;
//     }

//     public function headings(): array
//     {
//         return [
//             'No',
//             'Tanggal Transaksi',
//             'Pemasukan',
//             'Nominal Pemasukan',
//             'Pengeluaran',
//             'Nominal',
//             'Keterangan',
//         ];
//     }

//     public function styles(Worksheet $sheet)
//     {
//         $sheet->getStyle('A1:G1')->getFont()->setBold(true);

//         foreach (range('A', 'G') as $column) {
//             $sheet->getColumnDimension($column)->setAutoSize(true);
//         }
//     }
// }

// app/Exports/TransaksiExport.php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransaksiExport implements FromView
{
    public function __construct(
        public $transaksi,
        public $totalPemasukan,
        public $totalPengeluaran,
        public $netIncome
    ) {}

    public function view(): View
    {
        return view('transaksi.export_excel', [
            'transaksi' => $this->transaksi,
            'totalPemasukan' => $this->totalPemasukan,
            'totalPengeluaran' => $this->totalPengeluaran,
            'netIncome' => $this->netIncome,
        ]);
    }
}
