<?php

use App\Models\Transaksi;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transaksi = Transaksi::orderBy('id', 'desc')->first();

if (!$transaksi) {
    echo "No transactions found.\n";
    exit;
}

echo "ID: " . $transaksi->id . "\n";

$fields = ['pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan'];

foreach ($fields as $field) {
    $raw = DB::table('transaksi')->where('id', $transaksi->id)->value($field);
    echo "--- Field: $field ---\n";
    echo "Raw DB: " . $raw . "\n";
    echo "Model Value: " . $transaksi->$field . "\n";
    try {
        echo "Manual Decrypt: " . Crypt::decryptString($raw) . "\n";
    }
    catch (\Exception $e) {
        echo "Manual Decrypt Error: " . $e->getMessage() . "\n";
    }
}

echo "APP_KEY: " . config('app.key') . "\n";
Joseph:
