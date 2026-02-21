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
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->decimal('nominal_awal', 15, 2)->after('jumlah_pinjaman')->default(0);
            $table->decimal('nominal_sisa', 15, 2)->after('nominal_awal')->default(0);
            $table->integer('jumlah_angsuran')->after('nominal_sisa')->default(0);
            $table->integer('angsuran_ke')->after('jumlah_angsuran')->default(0);
            $table->integer('sisa_angsuran')->after('angsuran_ke')->default(0);
            $table->string('keterangan')->after('sisa_angsuran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropColumn([
                'nominal_awal',
                'nominal_sisa',
                'jumlah_angsuran',
                'angsuran_ke',
                'sisa_angsuran',
                'keterangan'
            ]);
        });
    }
};
