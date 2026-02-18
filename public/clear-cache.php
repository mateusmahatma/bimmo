<?php
/**
 * Script Pembersih Cache Laravel & OPcache
 * Upload file ini ke folder PUBLIC di hosting Anda (sejajar dengan index.php)
 * Lalu buka di browser: namadomain.com/clear-cache.php
 */

define('LARAVEL_START', microtime(true));

// Sesuaikan path ini jika struktur folder hosting Anda berbeda
// Defaultnya menganggap folder project ada di satu level di atas public_html
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Artisan;

echo "<html><body style='font-family: sans-serif; padding: 20px;'>";
echo "<h2>🧹 Bimmo Cache Cleaner</h2>";
echo "<hr>";

try {
    // 1. Clear Route Cache (Penting jika nambah route baru)
    Artisan::call('route:clear');
    echo "✅ <b>Route Cache:</b> Berhasil dihapus.<br>";

    // 2. Clear Config Cache (Penting jika ubah .env atau config)
    Artisan::call('config:clear');
    echo "✅ <b>Config Cache:</b> Berhasil dihapus.<br>";

    // 3. Clear Cache Aplikasi
    Artisan::call('cache:clear');
    echo "✅ <b>App Cache:</b> Berhasil dihapus.<br>";

    // 4. Clear View/Blade
    Artisan::call('view:clear');
    echo "✅ <b>View Cache:</b> Berhasil dihapus.<br>";

    // 5. Reset OPcache (PHP Cache)
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "✅ <b>OPcache (PHP):</b> Berhasil di-reset.<br>";
    }
    else {
        echo "ℹ️ <b>OPcache:</b> Tidak aktif atau tidak bisa diakses (abaikan).<br>";
    }

    echo "<hr>";
    echo "<h3 style='color: green;'>SEMUA BERSIH! ✨</h3>";
    echo "<p>Silakan coba tes Bot WhatsApp lagi sekarang.</p>";
    echo "<p style='color: red; font-size: small;'>PENTING: Segera hapus file ini dari hosting setelah selesai demi keamanan.</p>";

}
catch (\Exception $e) {
    echo "<h3 style='color: red;'>❌ Terjadi Kesalahan:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<br>Pastikan file ini diletakkan di folder yang benar (public_html/public).";
}

echo "</body></html>";
