<?php

use App\Models\Transaksi;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- PHASE 1: REPAIRING CORRUPTED DATA ---\n";

$fields = ['nominal_pemasukan', 'nominal', 'keterangan'];
$transactions = DB::table('transaksi')->get();
$repairedCount = 0;

foreach ($transactions as $t) {
    $updates = [];
    $corrupted = false;

    foreach ($fields as $f) {
        $val = $t->$f;
        // If it has value but doesn't look like Laravel encrypted JSON
        if ($val && strpos($val, '{"iv":') === false) {
            echo "Repairing ID {$t->id} field {$f}: $val\n";
            $updates[$f] = Crypt::encryptString($val);
            $corrupted = true;
        }
    }

    if ($corrupted) {
        DB::table('transaksi')->where('id', $t->id)->update($updates);
        $repairedCount++;
    }
}

echo "Repaired $repairedCount corrupted records.\n\n";

echo "--- PHASE 2: REFACTORING CONTROLLER ---\n";

$controllerFile = 'app/Http/Controllers/TransaksiController.php';
$content = file_get_contents($controllerFile);

$oldCode = '        Transaksi::where(\'id\', $id)
            ->where(\'id_user\', Auth::id())
            ->update($validatedData);';

$newCode = '        $transaksi = Transaksi::where(\'id\', $id)
            ->where(\'id_user\', Auth::id())
            ->firstOrFail();

        $transaksi->update($validatedData);';

if (strpos($content, $oldCode) !== false) {
    $content = str_replace($oldCode, $newCode, $content);
    file_put_contents($controllerFile, $content);
    echo "Successfully refactored TransaksiController@update\n";
}
else {
    // Try a more flexible regex replace if exact string fails
    $pattern = '/Transaksi::where\(\'id\', \$id\)\s+->where\(\'id_user\', Auth::id\(\)\)\s+->update\(\$validatedData\);/s';
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $newCode, $content);
        file_put_contents($controllerFile, $content);
        echo "Successfully refactored TransaksiController@update via Regex\n";
    }
    else {
        echo "WARNING: Could not find TransaksiController@update pattern to refactor!\n";
    }
}

echo "\n--- DONE ---\n";
Joseph:
