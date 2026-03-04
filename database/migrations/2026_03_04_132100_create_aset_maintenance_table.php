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
        Schema::create('aset_maintenance', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('id_aset')->constrained('aset')->onDelete('cascade');
            $blueprint->date('tanggal');
            $blueprint->string('kegiatan');
            $blueprint->string('teknisi')->nullable();
            $blueprint->decimal('biaya', 15, 2)->default(0);
            $blueprint->text('keterangan')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_maintenance');
    }
};
