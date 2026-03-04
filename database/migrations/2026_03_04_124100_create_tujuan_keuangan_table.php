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
        Schema::create('tujuan_keuangan', function (Blueprint $table) {
            $table->id('id_tujuan_keuangan');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('nama_target');
            $table->string('kategori');
            $table->decimal('nominal_target', 15, 2);
            $table->decimal('nominal_terkumpul', 15, 2)->default(0);
            $table->date('tenggat_waktu');
            $table->enum('prioritas', ['High', 'Medium', 'Low']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuan_keuangan');
    }
};
