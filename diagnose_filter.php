<?php

use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate a user if needed, or just fetch all
$userId = 1; // Assuming user ID 1 for diagnosis, or adjust as needed
Auth::loginUsingId($userId);

$start = '2026-01-01';
$end = '2026-01-31';
$filterId = '134';

echo "Diagnosing Transaksi filtering for User: $userId, Date: $start to $end, Filter ID: $filterId\n";

$all = Transaksi::where('id_user', $userId)
    ->whereBetween('tgl_transaksi', [$start, $end])
    ->get();

echo "Total transactions in range: " . $all->count() . "\n";

foreach ($all as $t) {
    $val = $t->pengeluaran;
    $type = gettype($val);
    echo "ID {$t->id}: pengeluaran = '$val' ($type)";
    if ($val == $filterId) {
        echo " MATCHES filter!";
    }
    echo "\n";
}
