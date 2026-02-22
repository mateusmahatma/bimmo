<?php
$fixes = [
    'app/Http/Controllers/DashboardController.php',
    'app/Http/Controllers/DanaDaruratController.php',
    'app/Http/Controllers/CompareController.php',
    'app/Http/Controllers/HasilProsesAnggaranController.php',
    'app/Http/Controllers/BarangController.php'
];

foreach ($fixes as $file) {
    if (!file_exists($file))
        continue;
    $content = file_get_contents($file);

    // Remove ->get()->get() if occurred
    $content = str_replace('->get()->get()', '->get()', $content);

    // Specifically fix cases where we called get() on a collection
    // This usually happens after a ->where() on a collection
    $content = str_replace('->where(\'status\', \'1\')->get()->sum', '->where(\'status\', \'1\')->sum', $content);
    $content = str_replace('->where(\'status\', 1)->get()->sum', '->where(\'status\', 1)->sum', $content);

    file_put_contents($file, $content);
    echo "Cleaned $file\n";
}
echo "Cleanup complete!";
