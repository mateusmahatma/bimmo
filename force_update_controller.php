<?php

$file = 'app/Http/Controllers/TransaksiController.php';
$content = file_get_contents($file);

$target = '            if (!empty($dataToInsert)) {
                Transaksi::insert($dataToInsert);
                $validDataFound = true;
                $processedCount += count($dataToInsert);
                break; // Stop after finding and processing the first valid sheet
            }';

$replacement = '            if (!empty($dataToInsert)) {
                // Use create() instead of insert() to trigger encryption casts
                foreach ($dataToInsert as $data) {
                    Transaksi::create($data);
                }
                $validDataFound = true;
                $processedCount += count($dataToInsert);
                break; // Stop after finding and processing the first valid sheet
            }';

if (strpos($content, $target) !== false) {
    echo "Found target. Replacing...\n";
    $newContent = str_replace($target, $replacement, $content);
    file_put_contents($file, $newContent);
    echo "Success!\n";
}
else {
    echo "Target NOT found in file.\n";
    // Let's try to find it with slightly different spacing
    $target2 = 'Transaksi::insert($dataToInsert);';
    if (strpos($content, $target2) !== false) {
        echo "Found partial target. Replacing partial...\n";
        $newContent = str_replace($target2, 'foreach ($dataToInsert as $data) { Transaksi::create($data); }', $content);
        file_put_contents($file, $newContent);
        echo "Success (partial)!\n";
    }
}
