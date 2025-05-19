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
        Schema::create('hasil_proses_anggaran', function (Blueprint $table) {
            $table->id('id_proses_anggaran');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('nama_anggaran');
            $table->json('jenis_pengeluaran');
            $table->decimal('persentase_anggaran', 5, 2); // Misalnya 25.50 (%)
            $table->decimal('nominal_anggaran', 15, 2); // Misalnya 1.000.000,00
            $table->decimal('anggaran_yang_digunakan', 15, 2);
            $table->decimal('sisa_anggaran', 15, 2);
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_proses_anggaran');
    }
};
