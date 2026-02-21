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
        Schema::table('bayar_pinjaman', function (Blueprint $table) {
            $table->string('bukti_bayar')->nullable()->after('tgl_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bayar_pinjaman', function (Blueprint $table) {
            $table->dropColumn('bukti_bayar');
        });
    }
};
