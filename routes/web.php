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
Route::get('/bimmo', [LoginController::class, 'index'])->name('bimmo')->middleware('guest');
Route::post('/bimmo', [LoginController::class, 'authenticate']);

// Log in Google
Route::get('/login/google', [GoogleLoginController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

// Daftar
Route::get('/daftar', [DaftarController::class, 'index']);
Route::post('/daftar', [DaftarController::class, 'store']);

// Dashboard
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/pie-data', [DashboardController::class, 'getPieData']);
    Route::get('/todayTransactions', [DashboardController::class, 'TodayTransactions']);
    Route::get('/line-data', [DashboardController::class, 'lineData']);
    Route::get('/jenis-pengeluaran', [DashboardController::class, 'getJenisPengeluaran']);
    Route::get('/transaksi', [DashboardController::class, 'getTransaksiByPengeluaran']);
    Route::get('/saving-rate-data', [DashboardController::class, 'getSavingRateData']);
});

Route::get('/logout', [DashboardController::class, 'logout']);

// Pemasukan
Route::middleware(['auth'])->group(function () {
    Route::resource('pemasukan', PemasukanController::class);
});

// Pengeluaran
Route::middleware(['auth'])->group(function () {
    Route::resource('pengeluaran', PengeluaranController::class);
});

// Transaksi
Route::middleware(['auth'])->group(function () {
    Route::prefix('transaksi')->group(function () {
        Route::get('/cetak_pdf', [TransaksiController::class, 'cetak_pdf']);
        Route::get('/download-excel', [TransaksiController::class, 'downloadExcel']);
        Route::post('/importExcel', [TransaksiController::class, 'importExcel'])->name('transaksi.importExcel');
        Route::post('/import', [TransaksiController::class, 'import'])->name('import-transaksi');
        Route::get('/download-template', [TransaksiController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/upload', [TransaksiController::class, 'upload'])->name('upload');
        Route::post('/{id}/toggle-status', [TransaksiController::class, 'toggleStatus'])->name('transaksi.toggleStatus');
    });

    Route::resource('transaksi', TransaksiController::class);
});

// Compare
Route::match(['get', 'post'], '/compare', [CompareController::class, 'index'])->middleware('auth');

// Barang
Route::resource('/barang', BarangController::class)->middleware('auth');
Route::get('/api/barang', [BarangController::class, 'getList'])->middleware('auth');



// Pinjaman
Route::middleware(['auth'])->group(function () {
    Route::resource('pinjaman', PinjamanController::class);
    Route::post('/pinjaman/{id}/bayar', [BayarPinjamanController::class, 'bayar'])->name('pinjaman.bayar');
    Route::delete('/bayar_pinjaman/{id}', [BayarPinjamanController::class, 'destroy'])->name('bayar_pinjaman.destroy');
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

// Ubah Password
Route::get('/ubah-password', [UbahPasswordController::class, 'index'])->middleware('auth');
Route::post('/ubah-password', [UbahPasswordController::class, 'store'])->middleware('auth');

// Lupa Password
Route::resource('/lupa-password', LupaPasswordController::class);

// Dana Darurat
Route::middleware('auth')->prefix('dana-darurat')->group(function () {
    Route::get('/', [DanaDaruratController::class, 'index']);
    Route::post('/', [DanaDaruratController::class, 'store']);
    Route::get('/{id}/edit', [DanaDaruratController::class, 'edit']);
    Route::put('/{id}', [DanaDaruratController::class, 'update']);
    Route::delete('/{id}', [DanaDaruratController::class, 'destroy']);
});

// Hasil Proses Anggaran
Route::resource('/hasil_proses_anggaran', HasilProsesAnggaranController::class)->middleware('auth');

// Update skin
Route::middleware(['auth'])->post('/user/skin', [UserController::class, 'updateSkin'])->name('user.update.skin');
