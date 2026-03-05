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
        Schema::create('dompet', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('ikon')->nullable();
            $table->text('saldo'); // Encrypted
            $table->unsignedBigInteger('id_user');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dompet');
    }
};
