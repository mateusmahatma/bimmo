<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

$t = Transaksi::first();
echo "Eloquent Model Data (Decrypted):" . PHP_EOL;
echo "ID: " . $t->id . PHP_EOL;
echo "Pemasukan: " . $t->pemasukan . PHP_EOL;
echo "Nominal Pemasukan: " . $t->nominal_pemasukan . PHP_EOL;
echo "Keterangan: " . $t->keterangan . PHP_EOL;

$raw = DB::table('transaksi')->where('id', $t->id)->first();
echo PHP_EOL . "Raw Database Data (Encrypted):" . PHP_EOL;
echo "Pemasukan: " . $raw->pemasukan . PHP_EOL;
echo "Keterangan: " . $raw->keterangan . PHP_EOL;
