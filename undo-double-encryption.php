<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- EMERGENCY RECOVERY: UNDO DOUBLE ENCRYPTION ---\n";

$fields = ['nominal_pemasukan', 'nominal', 'keterangan'];
$transactions = DB::table('transaksi')->get();
$undoneCount = 0;

foreach ($transactions as $t) {
    $updates = [];
    $changed = false;

    foreach ($fields as $f) {
        $val = $t->$f;
        if (!$val)
            continue;

        try {
            // Try to decrypt once
            $decryptedOnce = Crypt::decryptString($val);

            // Now, is the decrypted result ANOTHER encrypted string?
            // (Starts with eyJpdiIs which is {"iv": in base64)
            if (strpos($decryptedOnce, 'eyJpdiI') === 0) {
                echo "Undoing double encryption for ID {$t->id} field {$f}\n";
                $updates[$f] = $decryptedOnce; // Revert to the single-encrypted version
                $changed = true;
            }
        }
        catch (\Exception $e) {
        // Not encrypted at all or decryption failed, ignore
        }
    }

    if ($changed) {
        DB::table('transaksi')->where('id', $t->id)->update($updates);
        $undoneCount++;
    }
}

echo "Successfully reverted $undoneCount records to single-encryption.\n";
Joseph:
