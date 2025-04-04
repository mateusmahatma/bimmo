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
        Schema::create('dana_darurat', function (Blueprint $table) {
            $table->id('id_dana_darurat');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->date('tgl_transaksi_dana_darurat');
            $table->tinyInteger('jenis_transaksi_dana_darurat')->comment('1: Masuk, 2: Keluar');
            $table->decimal('nominal_dana_darurat', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dana_darurat');
    }
};
