<?php
$file = 'app/Http/Controllers/DashboardController.php';
$content = file_get_contents($file);

// 1. sum('nominal_pemasukan') and sum('nominal') to get()->sum(fn($t) => (float)$t->...)
$content = preg_replace(
    '/(Transaksi::where\(.*?\))\s*->sum\(\'nominal_pemasukan\'\)/s',
    '$1->get()->sum(fn($t) => (float)$t->nominal_pemasukan)',
    $content
);

$content = preg_replace(
    '/(Transaksi::where\(.*?\))\s*->sum\(\'nominal\'\)/s',
    '$1->get()->sum(fn($t) => (float)$t->nominal)',
    $content
);

// 2. Transaksi::where(...)->get()->sum('nominal') to Transaksi::where(...)->get()->sum(fn($t) => (float)$t->nominal)
$content = str_replace(
    "->sum('nominal_pemasukan')",
    "->sum(fn(\$t) => (float)\$t->nominal_pemasukan)",
    $content
);

$content = str_replace(
    "->sum('nominal')",
    "->sum(fn(\$t) => (float)\$t->nominal)",
    $content
);

// 3. Fix the cashflow SQL sum
$newCashflow = <<<'PHP'
$cashflow = Transaksi::where('id_user', Auth::id())
            ->where('tgl_transaksi', '>=', now()->subMonths($periode - 1)->startOfMonth())
            ->get()
            ->groupBy(function ($t) {
                return \Carbon\Carbon::parse($t->tgl_transaksi)->format('Y-m');
            })
            ->map(function ($items, $bulan) {
                return (object) [
                    'bulan' => $bulan,
                    'total_pemasukan' => $items->sum(fn($t) => (float)$t->nominal_pemasukan),
                    'total_pengeluaran' => $items->sum(fn($t) => (float)$t->nominal),
                    'selisih' => $items->sum(fn($t) => (float)$t->nominal_pemasukan) - $items->sum(fn($t) => (float)$t->nominal)
                ];
            })
            ->sortBy('bulan')
            ->values();
PHP;

$content = preg_replace(
    '/(\$cashflow = DB::table\(\'transaksi\'\).*?->get\(\).*?->map\(function \(\$row\) \{.*?\}\);)/s',
    $newCashflow,
    $content
);

// 4. Fix pengeluaranKategori SQL sum
$newKategori = <<<'PHP'
        $pengeluaranKategori = Transaksi::with('pengeluaranRelation')
            ->where('id_user', Auth::id())
            ->whereMonth('tgl_transaksi', $bulan)
            ->whereYear('tgl_transaksi', $tahun)
            ->whereNotNull('pengeluaran')
            ->get()
            ->groupBy('pengeluaran')
            ->map(function ($items) {
                return (object) [
                    'kategori' => $items->first()->pengeluaranRelation->nama ?? 'Unknown',
                    'total' => $items->sum(fn($t) => (float)$t->nominal)
                ];
            })
            ->sortByDesc('total')
            ->values();

        $totalPengeluaranBulan = $pengeluaranKategori->sum('total');
PHP;

$content = preg_replace(
    '/(\$pengeluaranKategori = DB::table\(\'transaksi\'\).*?\$totalPengeluaranBulan = \$pengeluaranKategori->sum\(\'total\'\);)/s',
    $newKategori,
    $content
);

file_put_contents($file, $content);
echo "Refactor complete!";
