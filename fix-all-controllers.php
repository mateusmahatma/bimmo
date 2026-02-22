<?php
$fixes = [
    'app/Http/Controllers/DanaDaruratController.php',
    'app/Http/Controllers/CompareController.php',
    'app/Http/Controllers/HasilProsesAnggaranController.php',
    'app/Http/Controllers/BarangController.php'
];

foreach ($fixes as $file) {
    if (!file_exists($file))
        continue;
    $content = file_get_contents($file);

    // Replace Transaksi::...->sum('nominal') to fetch and PHP sum
    // Pattern 1: Transaksi::where(...)->sum('nominal')
    $content = preg_replace(
        '/(Transaksi::where\(.*?\))\s*->sum\(\'nominal\'\)/s',
        '$1->get()->sum(fn($t) => (float)$t->nominal)',
        $content
    );

    // Pattern 2: Transaksi::where(...)->sum('nominal_pemasukan')
    $content = preg_replace(
        '/(Transaksi::where\(.*?\))\s*->sum\(\'nominal_pemasukan\'\)/s',
        '$1->get()->sum(fn($t) => (float)$t->nominal_pemasukan)',
        $content
    );

    // Pattern 3: Collection sum on 'nominal' or 'nominal_pemasukan'
    $content = str_replace(
        "->sum('nominal')",
        "->sum(fn(\$t) => (float)\$t->nominal)",
        $content
    );

    $content = str_replace(
        "->sum('nominal_pemasukan')",
        "->sum(fn(\$t) => (float)\$t->nominal_pemasukan)",
        $content
    );

    file_put_contents($file, $content);
    echo "Fixed $file\n";
}
echo "All remaining fixes applied!";
Joseph:
