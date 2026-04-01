<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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

    public function collection()
    {
        return $this->transaksi;
    }

    public function headings(): array
    {
        return [
            ["Cash Flow"],
            ["Total Income", number_format($this->totalPemasukan, 0, ",", ".")],
            ["Total Expense", number_format($this->totalPengeluaran, 0, ",", ".")],
            ["Net Balance", number_format($this->netIncome, 0, ",", ".")],
            [""], 
            [
                "No",
                "Date",
                "Income Category",
                "Income Amount",
                "Expense Category",
                "Expense Amount",
                "Description"
            ]
        ];
    }

    public function map($row): array
    {
        static $iteration = 0;
        $iteration++;

        $desc = $row->keterangan ?? "-";
        if ($row->keterangan) {
            $desc = preg_replace_callback("/<ol[^>]*>(.*?)<\/ol>/is", function($m) {
                $i = 1;
                return preg_replace_callback("/<li[^>]*>(.*?)<\/li>/is", function($li) use (&$i) {
                    return ($i++) . ". " . strip_tags($li[1]) . "\n";
                }, $m[1]);
            }, $desc);
            $desc = preg_replace_callback("/<ul[^>]*>(.*?)<\/ul>/is", function($m) {
                return preg_replace_callback("/<li[^>]*>(.*?)<\/li>/is", function($li) {
                    return "- " . strip_tags($li[1]) . "\n";
                }, $m[1]);
            }, $desc);
            $desc = preg_replace("/<\/?(p|br|div|h[1-6])[^>]*>/i", "\n", $desc);
            $desc = trim(strip_tags($desc));
            $desc = html_entity_decode($desc);
        }

        return [
            $iteration,
            $row->tgl_transaksi,
            $row->pemasukanRelation?->nama ?? "-",
            number_format((float)$row->nominal_pemasukan, 0, ",", "."),
            $row->pengeluaranRelation?->nama ?? "-",
            number_format((float)$row->nominal, 0, ",", "."),
            $desc
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ["font" => ["bold" => true, "size" => 14]],
            2 => ["font" => ["bold" => true]],
            3 => ["font" => ["bold" => true]],
            4 => ["font" => ["bold" => true]],
            6 => [
                "font" => ["bold" => true],
                "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
                "fill" => [
                    "fillType" => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    "startColor" => ["rgb" => "F2F2F2"]
                ]
            ],
        ];
    }
}

