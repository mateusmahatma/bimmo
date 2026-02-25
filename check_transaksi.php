<?php

use App\Models\Transaksi;
use Illuminate\Support\Facades\Crypt;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$broken = [];
$total = Transaksi::count();
echo "Checking $total records...\n";

foreach (Transaksi::all() as $t) {
    try {
        // Just try to access any encrypted field - Eloquent will try to decrypt
        $temp = $t->keterangan;
        $temp = $t->pemasukan;
        $temp = $t->nominal_pemasukan;
        $temp = $t->pengeluaran;
        $temp = $t->nominal;
    }
    catch (\Exception $e) {
        $broken[] = $t->id;
    }
}

foreach ($broken as $id) {
    $raw = Illuminate\Support\Facades\DB::table('transaksi')->where('id', $id)->first();
    echo "ID $id RAW DATA:\n";
    print_r($raw);
    echo "-------------------\n";
}

if (empty($broken)) {
    echo "NO BROKEN RECORDS FOUND\n";
}
else {
    echo "FOUND " . count($broken) . " BROKEN RECORDS: " . implode(', ', $broken) . "\n";
}
