<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Mock Auth if needed, but here we just pass id_user manually
$t = Transaksi::create([
    'tgl_transaksi' => date('Y-m-d'),
    'nominal' => 999,
    'id_user' => 1,
    'keterangan' => 'test encryption script'
]);

echo "ID Created: " . $t->id . "\n";
$raw = DB::table('transaksi')->where('id', $t->id)->first();
echo "Raw Keterangan in DB: " . $raw->keterangan . "\n";
echo "Eloquent Keterangan: " . $t->keterangan . "\n";

if (str_contains($raw->keterangan, 'eyJpdiI6')) {
    echo "SUCCESS: Data is encrypted in DB.\n";
}
else {
    echo "FAILURE: Data is PLAINTEXT in DB.\n";
}

$t->delete();
