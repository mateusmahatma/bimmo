<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

function checkModel($modelClass, $fields)
{
    echo "Checking " . $modelClass . "...\n";
    $brokenCount = 0;
    foreach ($modelClass::all() as $item) {
        foreach ($fields as $field) {
            try {
                $val = $item->$field;
            }
            catch (\Exception $e) {
                echo "BROKEN " . $modelClass . " ID " . $item->id . " Field '$field': " . $e->getMessage() . "\n";
                $brokenCount++;
                break; // One broken field is enough to flag the record
            }
        }
    }
    echo "Done checking " . $modelClass . ". Total broken: $brokenCount\n\n";
}

checkModel(User::class , ['name', 'email', 'no_hp', 'nominal_target_dana_darurat']);
checkModel(Transaksi::class , ['pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan']);
