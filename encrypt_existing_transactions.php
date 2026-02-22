<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

$count = DB::table('transaksi')->count();
echo "Starting encryption of $count transactions..." . PHP_EOL;

$transactions = DB::table('transaksi')->get();

foreach ($transactions as $index => $row) {
    DB::table('transaksi')->where('id', $row->id)->update([
        'pemasukan' => $row->pemasukan !== null ?Crypt::encryptString((string)$row->pemasukan) : null,
        'nominal_pemasukan' => $row->nominal_pemasukan !== null ?Crypt::encryptString((string)$row->nominal_pemasukan) : null,
        'pengeluaran' => $row->pengeluaran !== null ?Crypt::encryptString((string)$row->pengeluaran) : null,
        'nominal' => $row->nominal !== null ?Crypt::encryptString((string)$row->nominal) : null,
        'keterangan' => $row->keterangan !== null ?Crypt::encryptString((string)$row->keterangan) : null,
    ]);

    if (($index + 1) % 100 == 0) {
        echo "Processed " . ($index + 1) . " / $count" . PHP_EOL;
    }
}

echo "Encryption complete!" . PHP_EOL;
