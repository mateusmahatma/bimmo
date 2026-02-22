<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

/**
 * PRODUCTION ENCRYPTION SCRIPT FOR USERS TABLE
 * 
 * This script will:
 * 1. Identify users with plaintext data.
 * 2. Encrypt name, email, no_hp, and nominal_target_dana_darurat.
 * 3. Populate name_hash, email_hash, and no_hp_hash.
 * 4. Skip users that are already encrypted to prevent data loss.
 */

$count = DB::table('users')->count();
echo "Total users to process: $count" . PHP_EOL;

$users = DB::table('users')->get();
$processed = 0;
$skipped = 0;
$failed = 0;

foreach ($users as $row) {
    echo "--- User ID: {$row->id} ---" . PHP_EOL;

    // 1. Detect if email is already encrypted
    $isEncrypted = false;
    $plainEmail = $row->email;
    $plainName = $row->name;
    $plainNoHp = $row->no_hp;
    $plainNominal = $row->nominal_target_dana_darurat;

    try {
        // Test decryption
        if ($row->email) {
            $decryptedEmail = Crypt::decryptString($row->email);
            $isEncrypted = true;
            $plainEmail = $decryptedEmail;

            // Decrypt others if we can
            $plainName = $row->name ?Crypt::decryptString($row->name) : $row->name;
            $plainNoHp = $row->no_hp ?Crypt::decryptString($row->no_hp) : $row->no_hp;
            $plainNominal = $row->nominal_target_dana_darurat ?Crypt::decryptString($row->nominal_target_dana_darurat) : $row->nominal_target_dana_darurat;

            echo "Status: Already encrypted. Updating hashes only." . PHP_EOL;
        }
    }
    catch (\Exception $e) {
        $isEncrypted = false;
        echo "Status: Plaintext data detected. Encrypting now..." . PHP_EOL;
    }

    try {
        $updateData = [];

        if (!$isEncrypted) {
            // Perform Encryption
            $updateData['name'] = $plainName !== null ?Crypt::encryptString((string)$plainName) : null;
            $updateData['email'] = $plainEmail !== null ?Crypt::encryptString((string)$plainEmail) : null;
            $updateData['no_hp'] = $plainNoHp !== null ?Crypt::encryptString((string)$plainNoHp) : null;
            $updateData['nominal_target_dana_darurat'] = $plainNominal !== null ?Crypt::encryptString((string)$plainNominal) : null;
        }

        // Always update/populate hashes
        $updateData['name_hash'] = $plainName !== null ? hash('sha256', (string)$plainName) : null;
        $updateData['email_hash'] = $plainEmail !== null ? hash('sha256', (string)$plainEmail) : null;
        $updateData['no_hp_hash'] = $plainNoHp !== null ? hash('sha256', (string)$plainNoHp) : null;

        DB::table('users')->where('id', $row->id)->update($updateData);
        $processed++;
        echo "Result: Success!" . PHP_EOL;

    }
    catch (\Exception $e) {
        echo "Result: ERROR - " . $e->getMessage() . PHP_EOL;
        $failed++;
    }
}

echo PHP_EOL . "--- Final Summary ---" . PHP_EOL;
echo "Processed: $processed" . PHP_EOL;
echo "Skipped: $skipped" . PHP_EOL;
echo "Failed: $failed" . PHP_EOL;
echo "All users in production should now be encrypted and hashed." . PHP_EOL;
