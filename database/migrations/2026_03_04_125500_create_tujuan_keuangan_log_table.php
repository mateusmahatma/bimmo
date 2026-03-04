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
        Schema::create('tujuan_keuangan_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_tujuan_keuangan');
            $table->foreign('id_tujuan_keuangan')
                ->references('id_tujuan_keuangan')
                ->on('tujuan_keuangan')
                ->onDelete('cascade');
            $table->decimal('nominal_tambah', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuan_keuangan_log');
    }
};
