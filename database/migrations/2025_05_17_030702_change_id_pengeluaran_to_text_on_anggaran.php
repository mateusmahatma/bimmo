<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropForeign(['id_pengeluaran']); // hapus FK dengan nama kolom
        });

        Schema::table('anggaran', function (Blueprint $table) {
            $table->text('id_pengeluaran')->change();
        });
    }


    public function down()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengeluaran')->change();

            // Tambahkan lagi foreign key jika dibutuhkan
            $table->foreign('id_pengeluaran')->references('id')->on('pengeluaran')->onDelete('cascade');
        });
    }
};
