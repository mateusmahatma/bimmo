<?php

namespace App\Http\Controllers\Auth;

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

// Log in
Route::get('/', function () {
    return redirect()->route('bimmo');
})->name('login');
Route::get('/bimmo', [LoginController::class , 'index'])->name('bimmo')->middleware('guest');
Route::post('/bimmo', [LoginController::class , 'authenticate']);

// Log in Google
Route::get('/login/google', [GoogleLoginController::class , 'redirectToGoogle']);
Route::get('/login/google/callback', [GoogleLoginController::class , 'handleGoogleCallback']);

// Daftar
Route::get('/daftar', [DaftarController::class , 'index']);
Route::post('/daftar', [DaftarController::class , 'store']);

// Dashboard
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class , 'index'])->name('dashboard');
    Route::get('/chart-data', [DashboardController::class , 'getChartData']);
    Route::get('/pie-data', [DashboardController::class , 'getPieData']);
    Route::get('/todayTransactions', [DashboardController::class , 'TodayTransactions']);
    Route::get('/line-data', [DashboardController::class , 'lineData']);
    Route::get('/jenis-pengeluaran', [DashboardController::class , 'getJenisPengeluaran']);
    Route::get('/transaksi', [DashboardController::class , 'getTransaksiByPengeluaran']);
    Route::get('/saving-rate-data', [DashboardController::class , 'getSavingRateData']);
    Route::get('/anggaran/chart', [DashboardController::class , 'AnggaranChart'])->name('anggaran.chart');
    Route::post(
        '/dashboard/toggle-nominal-ajax',
    [DashboardController::class , 'toggleNominalAjax']
    )->name('dashboard.toggle-nominal.ajax');
    Route::get('/filter', [DashboardController::class , 'filter'])->name('dashboard.filter');
});

// Anggaran
Route::resource('/anggaran', AnggaranController::class)->middleware('auth');

// Proses Anggaran
Route::middleware('auth')->controller(FinancialCalculatorController::class)->prefix('kalkulator')->group(function () {
    Route::get('/', 'index')->name('kalkulator.index');
    Route::post('/', 'store')->name('kalkulator.store');
    Route::get('/{id}', 'show')->name('kalkulator.show');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::post('/calculate', 'calculate');
    Route::get('/calculate', 'showResult')->name('showResult');
    Route::get('/cetak_pdf', 'cetak_pdf');
});

// Aset
Route::resource('/barang', BarangController::class)->middleware('auth');
Route::get('/api/barang', [BarangController::class , 'getList'])->middleware('auth');

// Dana Darurat
Route::resource('/dana-darurat', DanaDaruratController::class)->middleware('auth');


// Pemasukan
Route::middleware(['auth'])->group(function () {
    Route::delete('/pemasukan/bulk-delete', [PemasukanController::class , 'bulkDelete'])->name('pemasukan.bulkDelete');
    Route::resource('pemasukan', PemasukanController::class);
});

// Anggaran
Route::middleware(['auth'])->group(function () {
    Route::delete('/anggaran/bulk-delete', [AnggaranController::class , 'bulkDelete'])->name('anggaran.bulkDelete');
    Route::resource('anggaran', AnggaranController::class);
});

// Pengeluaran
Route::middleware(['auth'])->group(function () {
    Route::delete('/pengeluaran/bulk-delete', [PengeluaranController::class , 'bulkDelete'])->name('pengeluaran.bulkDelete');
    Route::resource('pengeluaran', PengeluaranController::class);
});

// Transaksi
Route::middleware(['auth'])->group(function () {
    Route::prefix('transaksi')->group(function () {
            // web.php
            Route::get('/transaksi/export/pdf', [TransaksiController::class , 'exportPdf'])
                ->name('transaksi.export.pdf');

            Route::get('/transaksi/export/excel', [TransaksiController::class , 'exportExcel'])
                ->name('transaksi.export.excel');


            Route::post(
                '/transaksi/import-test',
            [TransaksiController::class , 'importTest']
            )->name('transaksi.importTest');


            Route::get('/transaksi/template', [TransaksiController::class , 'downloadTemplate'])
                ->name('transaksi.download.template');


            Route::post('/upload', [TransaksiController::class , 'upload'])->name('upload');
            Route::post('/{id}/toggle-status', [TransaksiController::class , 'toggleStatus'])->name('transaksi.toggleStatus');
        }
        );

        Route::delete('/transaksi/bulk-delete', [TransaksiController::class , 'bulkDelete'])->name('transaksi.bulkDelete');
        Route::resource('transaksi', TransaksiController::class)
            ->parameters(['transaksi' => 'hash']);
    });

// Compare
Route::match (['get', 'post'], '/compare', [CompareController::class , 'index'])->middleware('auth');

// Pinjaman
Route::middleware(['auth'])->group(function () {
    Route::delete('/pinjaman/bulk-delete', [PinjamanController::class , 'bulkDelete'])->name('pinjaman.bulkDelete');
    Route::resource('pinjaman', PinjamanController::class);
    Route::post('/pinjaman/{id}/bayar', [BayarPinjamanController::class , 'bayar'])->name('pinjaman.bayar');
    Route::delete('/bayar_pinjaman/{id}', [BayarPinjamanController::class , 'destroy'])->name('bayar_pinjaman.destroy');
});

// Ubah Password
Route::get('/ubah-password', [UbahPasswordController::class , 'index'])->middleware('auth');
Route::post('/ubah-password', [UbahPasswordController::class , 'store'])->middleware('auth');

// Lupa Password
Route::resource('/lupa-password', LupaPasswordController::class);
Route::get('/reset-password', [LupaPasswordController::class , 'resetIndex'])->name('password.reset');
Route::post('/reset-password', [LupaPasswordController::class , 'resetUpdate'])->name('password.update');

// Hasil Proses Anggaran
Route::resource('/hasil_proses_anggaran', HasilProsesAnggaranController::class)->middleware('auth');

// Update skin
Route::middleware(['auth'])->post('/user/skin', [UserController::class , 'updateSkin'])->name('user.update.skin');

Route::get('/check-session', function () {
    return response()->json(['alive' => true]);
})->middleware('auth');

Route::get('/logout', [DashboardController::class , 'logout']);
