<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$ids = [6214, 6215];
foreach ($ids as $id) {
    $row = DB::table('transaksi')->where('id', $id)->first();
    echo "ID: $id\n";
    if ($row) {
        foreach ($row as $key => $val) {
            echo "  $key: $val\n";
        }
    }
    else {
        echo "  NOT FOUND\n";
    }
    echo "-------------------\n";
}
