<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

$count = DB::table('users')->count();
echo "Starting encryption of $count users..." . PHP_EOL;

$users = DB::table('users')->get();

foreach ($users as $index => $row) {
    echo "Processing User ID: {$row->id} ({$row->email})" . PHP_EOL;

    // Check if it's already encrypted (very basic check)
    $isEncrypted = false;
    try {
        Crypt::decryptString($row->email);
        $isEncrypted = true;
    }
    catch (\Exception $e) {
        $isEncrypted = false;
    }

    if ($isEncrypted) {
        // Just update hashes if they are missing
        $plainEmail = Crypt::decryptString($row->email);
        $plainName = Crypt::decryptString($row->name);
        $plainNoHp = $row->no_hp ?Crypt::decryptString($row->no_hp) : null;

        DB::table('users')->where('id', $row->id)->update([
            'name_hash' => hash('sha256', (string)$plainName),
            'email_hash' => hash('sha256', (string)$plainEmail),
            'no_hp_hash' => $plainNoHp ? hash('sha256', (string)$plainNoHp) : null,
        ]);
        echo "Updated hashes for already encrypted user." . PHP_EOL;
    }
    else {
        $updateData = [
            'name' => $row->name !== null ?Crypt::encryptString((string)$row->name) : null,
            'email' => $row->email !== null ?Crypt::encryptString((string)$row->email) : null,
            'no_hp' => $row->no_hp !== null ?Crypt::encryptString((string)$row->no_hp) : null,
            'nominal_target_dana_darurat' => $row->nominal_target_dana_darurat !== null ?Crypt::encryptString((string)$row->nominal_target_dana_darurat) : null,
            'name_hash' => $row->name !== null ? hash('sha256', (string)$row->name) : null,
            'email_hash' => $row->email !== null ? hash('sha256', (string)$row->email) : null,
            'no_hp_hash' => $row->no_hp !== null ? hash('sha256', (string)$row->no_hp) : null,
        ];
        DB::table('users')->where('id', $row->id)->update($updateData);
        echo "Encrypted and updated user." . PHP_EOL;
    }

    if (($index + 1) % 10 == 0) {
        echo "Processed " . ($index + 1) . " / $count" . PHP_EOL;
    }
}

echo "Encryption and hashing complete!" . PHP_EOL;
