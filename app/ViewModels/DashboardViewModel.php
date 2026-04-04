<?php

namespace App\ViewModels;

class DashboardViewModel
{
    // Net Worth
    public float $totalAset;
    public float $totalDanaDarurat;
    public float $totalHutang;
    public float $netWorth;
    public string $netWorthFormatted;
    public float $assetPercent;
    public float $debtPercent;

    // Dana Darurat
    public float $targetDanaDarurat;
    public float $persentaseDanaDarurat;
    public float $sisaDanaDarurat;

    // Growth %
    public float $persenSaldo;
    public float $persenPemasukan;
    public float $persenPengeluaran;

    // Surplus/Deficit
    public float $totalNominalSisa;

    public function __construct(
        float $totalAset,
        float $totalDanaDarurat,
        float $totalHutang,
        float $targetDanaDarurat,
        float $pemasukan,
        float $pengeluaran,
        float $pemasukanLalu,
        float $pengeluaranLalu,
        float $saldo,
        float $saldoLalu,
    ) {
        $this->totalAset         = $totalAset;
        $this->totalDanaDarurat  = $totalDanaDarurat;
        $this->totalHutang       = $totalHutang;
        $this->targetDanaDarurat = $targetDanaDarurat;

        $this->computeNetWorth();
        $this->computeNetWorthBar();
        $this->computeEmergencyFund();

        $this->persenSaldo       = $this->computePercent($saldo, $saldoLalu);
        $this->persenPemasukan   = $this->computePercent($pemasukan, $pemasukanLalu);
        $this->persenPengeluaran = $this->computePercent($pengeluaran, $pengeluaranLalu);
        $this->totalNominalSisa  = $pemasukan - $pengeluaran;
    }

    private function computeNetWorth(): void
    {
        $this->netWorth = ($this->totalAset + $this->totalDanaDarurat) - $this->totalHutang;

        $formatted = 'Rp ' . number_format(abs($this->netWorth), 0, ',', '.');
        $this->netWorthFormatted = $this->netWorth < 0 ? '-' . $formatted : $formatted;
    }

    private function computeNetWorthBar(): void
    {
        $totalVal = ($this->totalAset + $this->totalDanaDarurat) + $this->totalHutang;

        $this->assetPercent = $totalVal > 0
            ? (($this->totalAset + $this->totalDanaDarurat) / $totalVal) * 100
            : 0;

        $this->debtPercent = $totalVal > 0
            ? ($this->totalHutang / $totalVal) * 100
            : 0;
    }

    private function computeEmergencyFund(): void
    {
        $this->persentaseDanaDarurat = $this->targetDanaDarurat > 0
            ? round(min(100, ($this->totalDanaDarurat / $this->targetDanaDarurat) * 100), 1)
            : 0;

        $this->sisaDanaDarurat = max(0, $this->targetDanaDarurat - $this->totalDanaDarurat);
    }

    private function computePercent(float $current, float $last): float
    {
        if ($last == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $last) / abs($last)) * 100, 1);
    }

    public function toArray(): array
    {
        return [
            'totalAset'             => $this->totalAset,
            'totalDanaDarurat'      => $this->totalDanaDarurat,
            'totalHutang'           => $this->totalHutang,
            'netWorth'              => $this->netWorth,
            'netWorthFormatted'     => $this->netWorthFormatted,
            'assetPercent'          => $this->assetPercent,
            'debtPercent'           => $this->debtPercent,
            'targetDanaDarurat'     => $this->targetDanaDarurat,
            'persentaseDanaDarurat' => $this->persentaseDanaDarurat,
            'sisaDanaDarurat'       => $this->sisaDanaDarurat,
            'persenSaldo'           => $this->persenSaldo,
            'persenPemasukan'       => $this->persenPemasukan,
            'persenPengeluaran'     => $this->persenPengeluaran,
            'totalNominalSisa'      => $this->totalNominalSisa,
        ];
    }
}
