<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

function checkModel($modelClass, $columns)
{
    echo "--- Checking Model: $modelClass ---" . PHP_EOL;
    try {
        $rows = $modelClass::all();
        foreach ($rows as $row) {
            foreach ($columns as $column) {
                try {
                    $val = $row->$column;
                }
                catch (\Exception $e) {
                    $raw = DB::table($row->getTable())->where('id', $row->id)->value($column);
                    echo "ID {$row->id}: Column '$column' DECRYPT FAIL. Raw: " . substr((string)$raw, 0, 30) . "..." . PHP_EOL;
                }
            }
        }
    }
    catch (\Exception $e) {
        echo "Error loading model $modelClass: " . $e->getMessage() . PHP_EOL;
    }
}

checkModel(App\Models\User::class , ['name', 'email', 'no_hp', 'nominal_target_dana_darurat']);
checkModel(App\Models\Transaksi::class , ['pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan']);
