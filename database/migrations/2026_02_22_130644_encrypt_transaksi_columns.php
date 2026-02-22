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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->text('pemasukan')->nullable()->change();
            $table->text('nominal_pemasukan')->nullable()->change();
            $table->text('pengeluaran')->nullable()->change();
            $table->text('nominal')->nullable()->change();
            $table->text('keterangan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('pemasukan')->nullable()->change();
            $table->decimal('nominal_pemasukan', 15, 2)->nullable()->change();
            $table->string('pengeluaran')->nullable()->change();
            $table->decimal('nominal', 15, 2)->nullable()->change();
            $table->string('keterangan')->nullable()->change();
        });
    }
};
