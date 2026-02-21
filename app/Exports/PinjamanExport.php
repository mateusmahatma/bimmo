<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;

class PinjamanExport implements FromView
{
    protected $filterStatus;

    public function __construct($filterStatus = null)
    {
        $this->filterStatus = $filterStatus;
    }

    public function view(): View
    {
        $query = Pinjaman::with('bayar_pinjaman')->where('id_user', Auth::id());

        if (!empty($this->filterStatus)) {
            $query->whereIn('status', (array)$this->filterStatus);
        }

        $pinjaman = $query->get();

        return view('pinjaman.export_excel', [
            'pinjaman' => $pinjaman,
        ]);
    }
}
