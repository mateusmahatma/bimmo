<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

// Fetch all transactions (triggering decryption)
$transactions = Transaksi::all();
$phpSum = $transactions->sum('nominal');

// Raw SQL Sum on encrypted data (should be 0 or incorrect)
$sqlSum = DB::table('transaksi')->sum('nominal');

echo "Summary Verification:" . PHP_EOL;
echo "PHP Sum (Correct): " . $phpSum . PHP_EOL;
echo "SQL Sum (Incorrect on encrypted data): " . $sqlSum . PHP_EOL;

if ($phpSum > 0 && $sqlSum == 0) {
    echo "SUCCESS: Summaries must be performed in PHP." . PHP_EOL;
}
else {
    echo "Note: SQL sum is $sqlSum. If data was successfully encrypted, this should not match the real total." . PHP_EOL;
}
