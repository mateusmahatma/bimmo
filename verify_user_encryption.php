<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

$user = User::find(1);

if (!$user) {
    echo "User not found!" . PHP_EOL;
    exit;
}

echo "Verification for User ID: {$user->id}" . PHP_EOL;
echo "Name (Decrypted): " . $user->name . PHP_EOL;
echo "Email (Decrypted): " . $user->email . PHP_EOL;
echo "No HP (Decrypted): " . $user->no_hp . PHP_EOL;
echo "Nominal (Decrypted): " . $user->nominal_target_dana_darurat . PHP_EOL;

$raw = DB::table('users')->where('id', $user->id)->first();
echo "--- Raw DB Data ---" . PHP_EOL;
echo "Email (Raw): " . substr($raw->email, 0, 20) . "..." . PHP_EOL;
echo "Email Hash: " . $raw->email_hash . PHP_EOL;

// Test lookup by hash
$hashToSearch = hash('sha256', 'mateusmahatma@gmail.com');
$foundUser = User::where('email_hash', $hashToSearch)->first();

if ($foundUser && $foundUser->id == $user->id) {
    echo "SUCCESS: Lookup by email_hash worked!" . PHP_EOL;
}
else {
    echo "FAILED: Lookup by email_hash failed!" . PHP_EOL;
}
