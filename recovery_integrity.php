<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting recovery of foreign keys (IDs)...\n";

$count = 0;
// We fetch raw records
$transactions = DB::table('transaksi')->get();

foreach ($transactions as $trans) {
    $updates = [];
    foreach (['pemasukan', 'pengeluaran'] as $field) {
        $val = $trans->$field;
        if (!$val || is_numeric($val))
            continue;

        try {
            // Laravel Encrypted strings are Base64 of a JSON string.
            // They often start with eyJ (Base64 for {" or similar)
            $decrypted = Crypt::decryptString($val);
            $updates[$field] = $decrypted;
            echo "ID {$trans->id}: Decrypted $field -> $decrypted\n";
        }
        catch (\Exception $e) {
        // Skip if not decryptable
        }
    }

    if (!empty($updates)) {
        DB::table('transaksi')->where('id', $trans->id)->update($updates);
        $count++;
    }
}

echo "Finished! Updated $count transactions.\n";
Joseph:
