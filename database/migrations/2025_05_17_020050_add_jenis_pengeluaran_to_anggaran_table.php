<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->foreignId('id_pengeluaran')->nullable()->constrained('pengeluaran')->onDelete('cascade')->after('nama_anggaran');
        });
    }

    public function down()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropForeign(['id_pengeluaran']);
            $table->dropColumn('id_pengeluaran');
        });
    }
};
