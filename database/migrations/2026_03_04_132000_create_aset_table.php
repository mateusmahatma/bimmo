<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $blueprint->string('kode_aset')->unique();
            $blueprint->string('nama_aset');
            $blueprint->string('kategori'); // IT, kendaraan, furnitur, dll
            $blueprint->string('merk_model')->nullable();
            $blueprint->string('nomor_seri')->nullable();
            $blueprint->date('tanggal_pembelian');
            $blueprint->decimal('harga_beli', 15, 2);
            $blueprint->integer('masa_pakai'); // dalam tahun
            $blueprint->decimal('nilai_sisa', 15, 2)->default(0); // nilai residu
            $blueprint->date('garansi_sampai')->nullable();
            $blueprint->string('kondisi'); // Baik, Kurang Baik, Rusak Berat, Hilang
            $blueprint->string('lokasi')->nullable();
            $blueprint->string('pic')->nullable();
            $blueprint->string('foto')->nullable();
            $blueprint->string('dokumen')->nullable();

            // Disposal fields
            $blueprint->boolean('is_disposed')->default(false);
            $blueprint->string('alasan_disposal')->nullable();
            $blueprint->date('tanggal_disposal')->nullable();
            $blueprint->decimal('nilai_disposal', 15, 2)->nullable();

            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
