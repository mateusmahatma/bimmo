<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\FinancialCalculatorController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\BayarPinjamanController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\LupaPasswordController;
use App\Http\Controllers\UbahPasswordController;
use App\Http\Controllers\DanaDaruratController;
use App\Http\Controllers\HasilProsesAnggaranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\TujuanKeuanganController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DompetController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ThreadCommentController;

// Authentication & Public Routes
Route::get('/', function () {
    return redirect()->route('bimmo');
})->name('login');
Route::get('/bimmo', [LoginController::class , 'index'])->name('bimmo')->middleware('guest');
Route::post('/bimmo', [LoginController::class , 'authenticate']);
Route::get('/login/google', [GoogleLoginController::class , 'redirectToGoogle']);
Route::get('/login/google/callback', [GoogleLoginController::class , 'handleGoogleCallback']);
Route::get('/daftar', [DaftarController::class , 'index']);
Route::post('/daftar', [DaftarController::class , 'store']);
Route::resource('/lupa-password', LupaPasswordController::class);
Route::get('/reset-password', [LupaPasswordController::class , 'resetIndex'])->name('password.reset');
Route::post('/reset-password', [LupaPasswordController::class , 'resetUpdate'])->name('password.update');

// Diagnostics
Route::get('/diagnose-encryption', function () {
    $results = [];
    $check = function ($tableName, $columns) use (&$results) {
            $rows = \Illuminate\Support\Facades\DB::table($tableName)->get();
            foreach ($rows as $row) {
                foreach ($columns as $column) {
                    if ($row->$column === null)
                        continue;
                    try {
                        \Illuminate\Support\Facades\Crypt::decryptString($row->$column);
                    }
                    catch (\Exception $e) {
                        $results[] = "[$tableName] ID {$row->id}: Column '$column' INVALID.";
                    }
                }
            }
        }
            ;
        $check('users', ['name', 'email', 'no_hp', 'nominal_target_dana_darurat']);
        $check('transaksi', ['pemasukan', 'nominal_pemasukan', 'pengeluaran', 'nominal', 'keterangan']);
        return count($results) > 0 ? implode("\n", $results) : "ALL OK";
    });

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard & Session
    Route::get(
        '/check-session',
        function () {
            return response()->json(['alive' => true]);
        }
        );
        Route::get('/logout', [DashboardController::class , 'logout']);
        Route::prefix('dashboard')->group(
            function () {
            Route::get('/', [DashboardController::class , 'index'])->name('dashboard');
            Route::get('/chart-data', [DashboardController::class , 'getChartData']);
            Route::get('/pie-data', [DashboardController::class , 'getPieData']);

            Route::get('/line-data', [DashboardController::class , 'lineData']);
            Route::get('/jenis-pengeluaran', [DashboardController::class , 'getJenisPengeluaran']);
            Route::get('/transaksi', [DashboardController::class , 'getTransaksiByPengeluaran']);
            Route::get('/filter', [DashboardController::class , 'filter'])->name('dashboard.filter');
            Route::get('/saving-rate-data', [DashboardController::class , 'getSavingRateData']);
            Route::get('/anggaran/chart', [DashboardController::class , 'AnggaranChart'])->name('anggaran.chart');
            Route::get('/net-worth-history', [DashboardController::class , 'getNetWorthHistory'])->name('dashboard.net-worth-history');
            Route::get('/net-worth', [DashboardController::class , 'netWorth'])->name('dashboard.net-worth');
            Route::post('/toggle-nominal-ajax', [DashboardController::class , 'toggleNominalAjax'])->name('dashboard.toggle-nominal.ajax');
        }
        );

        // Main Features (Calculators, Dana Darurat, Tujuan Keuangan)
        Route::controller(FinancialCalculatorController::class)->prefix('kalkulator')->group(
            function () {
            Route::get('/', 'index')->name('kalkulator.index');
            Route::post('/', 'store')->name('kalkulator.store');
            Route::delete('/bulk-delete', 'bulkDelete')->name('kalkulator.bulkDelete');
            Route::put('/bulk-sync', 'bulkSync')->name('kalkulator.bulkSync');
            Route::get('/{hash}', 'show')->name('kalkulator.show');
            Route::put('/{hash}', 'update');
            Route::delete('/{hash}', 'destroy');
            Route::post('/calculate', 'calculate');
            Route::get('/calculate/result', 'showResult')->name('showResult');
            Route::get('/cetak-pdf', 'cetak_pdf');
        }
        );

        Route::prefix('dana-darurat')->group(
            function () {
            Route::put('/target', [DanaDaruratController::class , 'updateTarget'])->name('dana-darurat.update-target');
            Route::delete('/bulk-delete', [DanaDaruratController::class , 'bulkDelete'])->name('dana-darurat.bulkDelete');
        }
        );
        Route::resource('dana-darurat', DanaDaruratController::class);

        Route::prefix('tujuan-keuangan')->group(
            function () {
            Route::post('/{id}/progress', [TujuanKeuanganController::class , 'updateProgress'])->name('tujuan-keuangan.update-progress');
            Route::get('/{id}/history', [TujuanKeuanganController::class , 'getHistory'])->name('tujuan-keuangan.history');
            Route::delete('/log/{id}', [TujuanKeuanganController::class , 'destroyLog'])->name('tujuan-keuangan.log.destroy');
        }
        );
        Route::resource('tujuan-keuangan', TujuanKeuanganController::class);

        // Assets & Events
        Route::prefix('aset')->group(
            function () {
            Route::get('/report', [AsetController::class , 'report'])->name('aset.report');
            Route::post('/{id}/maintenance', [AsetController::class , 'addMaintenance'])->name('aset.maintenance.store');
            Route::get('/maintenance/{id}/edit', [AsetController::class , 'editMaintenance'])->name('aset.maintenance.edit');
            Route::put('/maintenance/{id}', [AsetController::class , 'updateMaintenance'])->name('aset.maintenance.update');
            Route::delete('/maintenance/{id}', [AsetController::class , 'destroyMaintenance'])->name('aset.maintenance.destroy');
            Route::post('/{id}/dispose', [AsetController::class , 'dispose'])->name('aset.dispose');

            // Storage access for assets (consistent with profile photos)
            Route::get('/storage/document/{filename}', [AsetController::class , 'showDocument'])->name('storage.aset_document');
            Route::get('/storage/maintenance/{filename}', [AsetController::class , 'showMaintenanceDocument'])->name('storage.maintenance_document');
        }
        );

        Route::resource('aset', AsetController::class);
        Route::resource('events', EventController::class);
        Route::resource('barang', BarangController::class);
        Route::resource('notes', NoteController::class);

        // Threads
        Route::resource('threads', ThreadController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::post('threads/{id}/comments', [ThreadCommentController::class , 'store'])->name('threads.comments.store');
        Route::delete('threads/comments/{id}', [ThreadCommentController::class , 'destroy'])->name('threads.comments.destroy');

        // Pemasukan, Pengeluaran, Anggaran
        Route::delete('/pemasukan/bulk-delete', [PemasukanController::class , 'bulkDelete'])->name('pemasukan.bulkDelete');
        Route::resource('pemasukan', PemasukanController::class);
        Route::delete('/pengeluaran/bulk-delete', [PengeluaranController::class , 'bulkDelete'])->name('pengeluaran.bulkDelete');
        Route::resource('pengeluaran', PengeluaranController::class);
        Route::delete('/anggaran/bulk-delete', [AnggaranController::class , 'bulkDelete'])->name('anggaran.bulkDelete');
        Route::resource('anggaran', AnggaranController::class);

        // Dompet (Wallet)
        Route::get('/dompet/reports', [DompetController::class , 'reports'])->name('dompet.reports');
        Route::post('/dompet/{id}/add-balance', [DompetController::class , 'addBalance'])->name('dompet.add-balance');
        Route::resource('dompet', DompetController::class);

        // Transaksi
        Route::prefix('transaksi')->group(
            function () {
            Route::get('/export/pdf', [TransaksiController::class , 'exportPdf'])->name('transaksi.export.pdf');
            Route::get('/export/excel', [TransaksiController::class , 'exportExcel'])->name('transaksi.export.excel');
            Route::get('/export/email', [TransaksiController::class , 'emailExcel'])->name('transaksi.export.email');
            Route::get('/template', [TransaksiController::class , 'downloadTemplate'])->name('transaksi.download.template');
            Route::post('/import', [TransaksiController::class , 'importTest'])->name('transaksi.importTest');
            Route::post('/upload', [TransaksiController::class , 'upload'])->name('upload');
            Route::delete('/bulk-delete', [TransaksiController::class , 'bulkDelete'])->name('transaksi.bulkDelete');
            Route::delete('/{id}/file', [TransaksiController::class , 'deleteFile'])->name('transaksi.deleteFile');
            Route::post('/{id}/toggle-status', [TransaksiController::class , 'toggleStatus'])->name('transaksi.toggleStatus');
            Route::get('/date/{date}', [TransaksiController::class , 'showByDate'])->name('transaksi.byDate');
        }
        );
        Route::resource('transaksi', TransaksiController::class)->parameters(['transaksi' => 'hash']);

        // Pinjaman & More
        Route::delete('/pinjaman/bulk-delete', [PinjamanController::class , 'bulkDelete'])->name('pinjaman.bulkDelete');
        Route::get('/pinjaman/export/excel', [PinjamanController::class , 'exportExcel'])->name('pinjaman.export.excel');
        Route::resource('pinjaman', PinjamanController::class)->parameters(['pinjaman' => 'hash']);
        Route::post('/pinjaman/{hash}/bayar', [BayarPinjamanController::class , 'bayar'])->name('pinjaman.bayar');
        Route::resource('bayar-pinjaman', BayarPinjamanController::class)->except(['index', 'create', 'store', 'show']);
        Route::match (['get', 'post'], '/compare', [CompareController::class , 'index'])->name('compare');

        // User Management
        Route::get('/profil', [UserController::class , 'index'])->name('profil.index');
        Route::put('/profil/name', [UserController::class , 'updateName'])->name('profil.updateName');
        Route::put('/profil/password', [UserController::class , 'updatePassword'])->name('profil.updatePassword');
        Route::put('/profil/email', [UserController::class , 'updateEmail'])->name('profil.updateEmail');
        Route::put('/profil/phone', [UserController::class , 'updatePhoneNumber'])->name('profil.updatePhoneNumber');
        Route::put('/profil/photo', [UserController::class , 'updatePhoto'])->name('profil.updatePhoto');
        Route::delete('/profil/photo', [UserController::class , 'deletePhoto'])->name('profil.deletePhoto');
        Route::get('/storage/profile-photo/{filename}', [UserController::class , 'showPhoto'])->name('storage.profile_photo');
        Route::post('/user/language', [UserController::class , 'updateLanguage'])->name('user.update.language');
        Route::post('/user/skin', [UserController::class , 'updateSkin'])->name('user.update.skin');
        Route::post('/user/ui-style', [UserController::class , 'updateUiStyle'])->name('user.update.ui-style');
        Route::get('/ubah-password', [UbahPasswordController::class , 'index']);
        Route::post('/ubah-password', [UbahPasswordController::class , 'store']);
        Route::post('/feedback', [FeedbackController::class , 'store'])->name('feedback.store');

        // Subscription
        Route::post('/subscription/subscribe', [SubscriptionController::class , 'subscribe'])->name('subscription.subscribe');
        Route::post('/subscription/cancel', [SubscriptionController::class , 'cancel'])->name('subscription.cancel');
        Route::post('/subscription/webhook', [SubscriptionController::class , 'webhook'])->name('subscription.webhook');

        Route::resource('hasil_proses_anggaran', HasilProsesAnggaranController::class);
        Route::get('/panduan', [App\Http\Controllers\UserGuideController::class , 'index'])->name('panduan.index');

        // Migration Features
        Route::prefix('panduan/pindah')->group(
            function () {
            Route::get('/', [MigrationController::class , 'index'])->name('panduan.pindah');
            Route::get('/template/{type}', [MigrationController::class , 'downloadTemplate'])->name('panduan.pindah.template');
            Route::post('/upload', [MigrationController::class , 'upload'])->name('panduan.pindah.upload');
        }
        );
    });

// App Webhook
Route::post('/api/webhook/whatsapp', [\App\Http\Controllers\Api\WhatsAppController::class , 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
