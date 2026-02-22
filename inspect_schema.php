<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$table = 'transaksi';
$columns = Schema::getColumnListing($table);
foreach ($columns as $col) {
    echo $col . ': ' . Schema::getColumnType($table, $col) . PHP_EOL;
}
