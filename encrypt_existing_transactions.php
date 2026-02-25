<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

$count = DB::table('transaksi')->count();
echo "Starting safe encryption of $count transactions..." . PHP_EOL;

$transactions = DB::table('transaksi')->get();
$processed = 0;
$skipped = 0;

foreach ($transactions as $index => $row) {
    if ($index % 100 == 0) {
        echo "Processing batch starting at $index..." . PHP_EOL;
    }

    $updateData = [];
    $cols = ['pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan'];

    foreach ($cols as $col) {
        $val = $row->$col;
        if ($val === null)
            continue;

        // Check if already encrypted
        $isAlreadyEncrypted = false;
        try {
            Crypt::decryptString($val);
            $isAlreadyEncrypted = true;
        }
        catch (\Exception $e) {
            $isAlreadyEncrypted = false;
        }

        if (!$isAlreadyEncrypted) {
            $updateData[$col] = Crypt::encryptString((string)$val);
        }
    }

    if (!empty($updateData)) {
        DB::table('transaksi')->where('id', $row->id)->update($updateData);
        $processed++;
    }
    else {
        $skipped++;
    }
}

echo "Encryption complete!" . PHP_EOL;
echo "Processed (New Encryptions): $processed" . PHP_EOL;
echo "Skipped (Already Encrypted): $skipped" . PHP_EOL;
