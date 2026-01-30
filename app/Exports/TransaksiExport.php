<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransaksiExport implements FromView
{
    protected $transaksi;
    protected $totalPemasukan;
    protected $totalPengeluaran;
    protected $netIncome;

    public function __construct($transaksi, $totalPemasukan, $totalPengeluaran, $netIncome)
    {
        $this->transaksi = $transaksi;
        $this->totalPemasukan = $totalPemasukan;
        $this->totalPengeluaran = $totalPengeluaran;
        $this->netIncome = $netIncome;
    }

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
