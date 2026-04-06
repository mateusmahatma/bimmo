<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\Pinjaman;
use App\Models\Aset;
use App\Models\DanaDarurat;
use App\Models\User;
use Carbon\Carbon;

/**
 * Menangani semua query DB untuk kebutuhan Dashboard.
 * Controller tinggal memanggil service ini, bukan query langsung.
 */
class DashboardDataService
{
    public function __construct(private int $userId) {}

    // -------------------------------------------------------------------------
    // Numbers (saldo, pemasukan, pengeluaran, perbandingan bulan lalu)
    // -------------------------------------------------------------------------
    public function getDashboardNumbers(): array
    {
        $now       = Carbon::now('Asia/Jakarta');
        $lastMonth = $now->copy()->subMonthsNoOverflow(1);

        // Bulan ini
        $transaksiIni = Transaksi::where('id_user', $this->userId)
            ->whereMonth('tgl_transaksi', $now->month)
            ->whereYear('tgl_transaksi', $now->year)
            ->get();

        $pemasukanBulanIni   = $transaksiIni->sum(fn($t) => (float) $t->nominal_pemasukan);
        $pengeluaranBulanIni = $transaksiIni->sum(fn($t) => (float) $t->nominal);
        $saldo               = $pemasukanBulanIni - $pengeluaranBulanIni;

        // Bulan lalu (MTD — fair comparison)
        $transaksiLalu = Transaksi::where('id_user', $this->userId)
            ->whereBetween('tgl_transaksi', [
                $lastMonth->copy()->startOfMonth(),
                $lastMonth,
            ])
            ->get();

        $pemasukanBulanLalu   = $transaksiLalu->sum(fn($t) => (float) $t->nominal_pemasukan);
        $pengeluaranBulanLalu = $transaksiLalu->sum(fn($t) => (float) $t->nominal);
        $saldoBulanLalu       = $pemasukanBulanLalu - $pengeluaranBulanLalu;

        // Hari ini
        $pengeluaranHariIni = Transaksi::where('id_user', $this->userId)
            ->whereDate('tgl_transaksi', $now->toDateString())
            ->get()
            ->sum(fn($t) => (float) $t->nominal);

        // Cicilan
        $cicilanBesok = Pinjaman::where('id_user', $this->userId)
            ->where('status', 'belum_lunas')
            ->get()
            ->sum(fn($p) => min($p->nominal_angsuran, $p->jumlah_pinjaman));

        return [
            'saldo'               => $saldo,
            'pemasukan'           => $pemasukanBulanIni,
            'pengeluaran'         => $pengeluaranBulanIni,
            'hari_ini'            => $pengeluaranHariIni,
            'cicilan_besok'       => $cicilanBesok,
            'pemasukan_lalu'      => $pemasukanBulanLalu,
            'pengeluaran_lalu'    => $pengeluaranBulanLalu,
            'saldo_lalu'          => $saldoBulanLalu,
        ];
    }

    // -------------------------------------------------------------------------
    // Aset, Hutang, Dana Darurat
    // -------------------------------------------------------------------------
    public function getWealthData(): array
    {
        $totalAset = (float) Aset::where('id_user', $this->userId)
            ->where('is_disposed', false)
            ->sum('harga_beli');

        $totalHutang = (float) Pinjaman::where('id_user', $this->userId)
            ->sum('jumlah_pinjaman');

        $totalMasukDD  = DanaDarurat::where('id_user', $this->userId)->where('jenis_transaksi_dana_darurat', 1)->sum('nominal_dana_darurat');
        $totalKeluarDD = DanaDarurat::where('id_user', $this->userId)->where('jenis_transaksi_dana_darurat', 2)->sum('nominal_dana_darurat');
        $totalDanaDarurat = (float) ($totalMasukDD - $totalKeluarDD);

        $totalSaldoDompet = (float) \App\Models\Dompet::where('id_user', $this->userId)->get()->sum('saldo');

        return [
            'totalAset'        => $totalAset,
            'totalHutang'      => $totalHutang,
            'totalDanaDarurat' => $totalDanaDarurat,
            'totalSaldoDompet' => $totalSaldoDompet,
        ];
    }

    // -------------------------------------------------------------------------
    // Target Dana Darurat (manual vs otomatis)
    // -------------------------------------------------------------------------
    public function getTargetDanaDarurat(): float
    {
        $user = User::find($this->userId);

        if ($user->metode_target_dana_darurat === 'manual') {
            return (float) ($user->nominal_target_dana_darurat ?? 0);
        }

        $allTrx = Transaksi::where('id_user', $this->userId)->orderBy('tgl_transaksi')->get();

        $totalPengeluaran = $allTrx->where('status', '1')->sum(fn($t) => (float) $t->nominal);

        if ($allTrx->count() > 1) {
            $firstDate    = Carbon::parse($allTrx->first()->tgl_transaksi)->startOfMonth();
            $lastDate     = Carbon::parse($allTrx->last()->tgl_transaksi)->startOfMonth();
            $selisihBulan = $firstDate->diffInMonths($lastDate) + 1;
        } else {
            $selisihBulan = 1;
        }

        $rataRata  = $selisihBulan > 0 ? $totalPengeluaran / $selisihBulan : 0;
        $kelipatan = $user->kelipatan_target_dana_darurat ?? 6;

        return (float) ($rataRata * $kelipatan);
    }

    // -------------------------------------------------------------------------
    // Cashflow per periode
    // -------------------------------------------------------------------------
    public function getCashflow(int $periode)
    {
        return Transaksi::where('id_user', $this->userId)
            ->where('tgl_transaksi', '>=', now()->subMonths($periode - 1)->startOfMonth())
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->tgl_transaksi)->format('Y-m'))
            ->map(fn($items, $bulan) => (object) [
                'bulan'             => $bulan,
                'total_pemasukan'   => $items->sum(fn($t) => (float) $t->nominal_pemasukan),
                'total_pengeluaran' => $items->sum(fn($t) => (float) $t->nominal),
                'selisih'           => $items->sum(fn($t) => (float) $t->nominal_pemasukan) - $items->sum(fn($t) => (float) $t->nominal),
            ])
            ->sortBy('bulan')
            ->values();
    }

    // -------------------------------------------------------------------------
    // Pengeluaran per kategori
    // -------------------------------------------------------------------------
    public function getPengeluaranKategori(int $bulan, int $tahun)
    {
        $rows = Transaksi::with('pengeluaranRelation')
            ->where('id_user', $this->userId)
            ->whereMonth('tgl_transaksi', $bulan)
            ->whereYear('tgl_transaksi', $tahun)
            ->get()
            ->filter(fn($t) => !empty($t->pengeluaran))
            ->groupBy('pengeluaran')
            ->map(fn($items) => (object) [
                'kategori' => $items->first()->pengeluaranRelation->nama ?? 'Unknown',
                'total'    => $items->sum(fn($t) => (float) $t->nominal),
            ])
            ->sortByDesc('total')
            ->values();

        $totalBulan = $rows->sum('total');

        return [
            'pengeluaranKategori'   => $rows->map(function ($row) use ($totalBulan) {
                $row->persen = $totalBulan > 0 ? round(($row->total / $totalBulan) * 100, 1) : 0;
                return $row;
            }),
            'totalPengeluaranBulan' => $totalBulan,
        ];
    }

    // -------------------------------------------------------------------------
    // Transaksi hari ini
    // -------------------------------------------------------------------------
    public function getTransaksiHariIni(): array
    {
        $rawTransaksi = Transaksi::with(['pemasukanRelation', 'pengeluaranRelation'])
            ->where('id_user', $this->userId)
            ->whereDate('tgl_transaksi', now())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $transaksiHariIni = collect();

        foreach ($rawTransaksi as $row) {
            if ((float) $row->nominal_pemasukan > 0) {
                $transaksiHariIni->push((object) [
                    'waktu'      => $row->created_at,
                    'jenis'      => 'pemasukan',
                    'kategori'   => $row->pemasukanRelation->nama ?? '-',
                    'keterangan' => $row->keterangan,
                    'nominal'    => (float) $row->nominal_pemasukan,
                ]);
            }

            if ((float) $row->nominal > 0) {
                $transaksiHariIni->push((object) [
                    'waktu'      => $row->created_at,
                    'jenis'      => 'pengeluaran',
                    'kategori'   => $row->pengeluaranRelation->nama ?? '-',
                    'keterangan' => $row->keterangan,
                    'nominal'    => (float) $row->nominal,
                ]);
            }
        }

        return [
            'transaksiHariIni'   => $transaksiHariIni,
            'totalMasukHariIni'  => $transaksiHariIni->where('jenis', 'pemasukan')->sum('nominal'),
            'totalKeluarHariIni' => $transaksiHariIni->where('jenis', 'pengeluaran')->sum('nominal'),
        ];
    }

    // -------------------------------------------------------------------------
    // Rasio keuangan
    // -------------------------------------------------------------------------
    public function getRasioData(mixed $cashflow): array
    {
        $now       = Carbon::now('Asia/Jakarta');
        $lastMonth = $now->copy()->subMonthsNoOverflow(1);

        $totalPendapatan     = $cashflow->sum('total_pemasukan');
        $totalPengeluaranAll = $cashflow->sum('total_pengeluaran');
        $expenseRatio        = $totalPendapatan > 0
            ? round(($totalPengeluaranAll / $totalPendapatan) * 100, 2)
            : 0;

        $danaDarurat       = DanaDarurat::where('id_user', $this->userId)->value('nominal_dana_darurat') ?? 0;
        $rataPengeluaran   = $cashflow->count() > 0 ? $cashflow->avg('total_pengeluaran') : 0;
        $danaDaruratBulan  = $rataPengeluaran > 0 ? round($danaDarurat / $rataPengeluaran, 1) : 0;

        $totalAsetPhysical = (float) Aset::where('id_user', $this->userId)->where('is_disposed', false)->sum('harga_beli');
        $totalPinjaman     = (float) Pinjaman::where('id_user', $this->userId)->sum('jumlah_pinjaman');
        $rasio             = $totalAsetPhysical > 0 ? ($totalPinjaman / $totalAsetPhysical) * 100 : 0;

        $totalThisMonth = Transaksi::where('id_user', $this->userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float) $t->nominal);
        $totalLastMonth = Transaksi::where('id_user', $this->userId)->where('status', '1')->whereYear('tgl_transaksi', $lastMonth->year)->whereMonth('tgl_transaksi', $lastMonth->month)->get()->sum(fn($t) => (float) $t->nominal);
        $rasio_inflasi  = $totalLastMonth != 0 ? (($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100 : 0;

        $totalPemasukanMonth   = Transaksi::where('id_user', $this->userId)->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float) $t->nominal_pemasukan);
        $totalPengeluaranMonth = Transaksi::where('id_user', $this->userId)->where('status', '1')->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month)->get()->sum(fn($t) => (float) $t->nominal);
        $rasio_pengeluaran_pendapatan = $totalPemasukanMonth > 0
            ? ($totalPengeluaranMonth / $totalPemasukanMonth) * 100
            : 0;

        return [
            'expenseRatio'                 => $expenseRatio,
            'danaDaruratBulan'             => $danaDaruratBulan,
            'rasio'                        => $rasio,
            'rasio_inflasi'                => $rasio_inflasi,
            'rasio_pengeluaran_pendapatan' => $rasio_pengeluaran_pendapatan,
            'totalAsetPhysical'            => $totalAsetPhysical,
            'totalPinjaman'                => $totalPinjaman,
        ];
    }
}
