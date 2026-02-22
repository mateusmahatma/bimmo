<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

/**
 * PRODUCTION ENCRYPTION SCRIPT FOR TRANSAKSI TABLE
 * 
 * This script will:
 * 1. Identify transactions with plaintext data.
 * 2. Encrypt pemasukan, nominal_pemasukan, pengeluaran, nominal, and keterangan.
 * 3. Skip transactions that are already encrypted.
 */

$count = DB::table('transaksi')->count();
echo "Total transactions to process: $count" . PHP_EOL;

$transactions = DB::table('transaksi')->get();
$processed = 0;
$failed = 0;

foreach ($transactions as $index => $row) {
    if ($index % 100 == 0) {
        echo "Processing batch starting at index $index..." . PHP_EOL;
    }

    // Detect if already encrypted (checking 'nominal' as proxy)
    $isEncrypted = false;
    try {
        if ($row->nominal !== null) {
            Crypt::decryptString($row->nominal);
            $isEncrypted = true;
        }
        elseif ($row->nominal_pemasukan !== null) {
            Crypt::decryptString($row->nominal_pemasukan);
            $isEncrypted = true;
        }
    }
    catch (\Exception $e) {
        $isEncrypted = false;
    }

    if ($isEncrypted) {
        continue; // Skip already encrypted
    }

    try {
        DB::table('transaksi')->where('id', $row->id)->update([
            'pemasukan' => $row->pemasukan !== null ?Crypt::encryptString((string)$row->pemasukan) : null,
            'nominal_pemasukan' => $row->nominal_pemasukan !== null ?Crypt::encryptString((string)$row->nominal_pemasukan) : null,
            'pengeluaran' => $row->pengeluaran !== null ?Crypt::encryptString((string)$row->pengeluaran) : null,
            'nominal' => $row->nominal !== null ?Crypt::encryptString((string)$row->nominal) : null,
            'keterangan' => $row->keterangan !== null ?Crypt::encryptString((string)$row->keterangan) : null,
        ]);
        $processed++;
    }
    catch (\Exception $e) {
        echo "ERROR for Transaksi ID {$row->id}: " . $e->getMessage() . PHP_EOL;
        $failed++;
    }
}

echo PHP_EOL . "--- Final Summary ---" . PHP_EOL;
echo "Transactions Encrypted: $processed" . PHP_EOL;
echo "Failed: $failed" . PHP_EOL;
echo "All transactions should now be encrypted." . PHP_EOL;
