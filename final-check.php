<?php

use App\Models\Transaksi;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- FINAL INTEGRITY CHECK ---\n";

// Check the one we fixed from plain text
$t6206 = Transaksi::find(6206);
if ($t6206) {
    echo "ID 6206 Status:\n";
    echo "  Nominal (Model): " . $t6206->nominal . "\n";
    echo "  Keterangan (Model): " . $t6206->keterangan . "\n";
}
else {
    echo "ID 6206 not found, checking latest instead.\n";
}

// Check a random existing one
$tRandom = Transaksi::where('id', '!=', 6206)->first();
if ($tRandom) {
    echo "ID {$tRandom->id} Status:\n";
    echo "  Nominal (Model): " . $tRandom->nominal . "\n";
    echo "  Keterangan (Model): " . $tRandom->keterangan . "\n";
}

// Check if any still look like plain text or double encrypted
$fields = ['nominal_pemasukan', 'nominal', 'keterangan'];
$raws = DB::table('transaksi')->get();
$badCount = 0;
foreach ($raws as $r) {
    foreach ($fields as $f) {
        $val = $r->$f;
        if (!$val)
            continue;

        // Check if NOT encrypted
        if (strpos($val, 'eyJpdiI') !== 0) {
            $badCount++;
            echo "STILL PLAIN TEXT: ID {$r->id} field $f: $val\n";
        }

        // Check for double encryption (decrypted still looks like encrypted)
        try {
            $dec = Crypt::decryptString($val);
            if (strpos($dec, 'eyJpdiI') === 0) {
                $badCount++;
                echo "STILL DOUBLE ENCRYPTED: ID {$r->id} field $f\n";
            }
        }
        catch (\Exception $e) {
        // If it can't decrypt, and it starts with eyJ, it's just one encryption level but maybe invalid payload.
        // But we checked that above.
        }
    }
}

echo "\nTotal Integrity Issues Found: $badCount\n";
Joseph:
