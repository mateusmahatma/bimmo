<?php
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