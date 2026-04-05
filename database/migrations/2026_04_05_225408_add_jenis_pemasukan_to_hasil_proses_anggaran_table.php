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
        Schema::table('hasil_proses_anggaran', function (Blueprint $table) {
            $table->json('jenis_pemasukan')->nullable()->after('nama_anggaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_proses_anggaran', function (Blueprint $table) {
            $table->dropColumn('jenis_pemasukan');
        });
    }
};
