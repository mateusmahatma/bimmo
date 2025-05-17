<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign('anggaran_id_pengeluaran_foreign');

            // Baru hapus kolomnya
            $table->dropColumn('id_pengeluaran');
        });

        Schema::table('anggaran', function (Blueprint $table) {
            // Tambahkan ulang sebagai TEXT (untuk menyimpan array ID)
            $table->text('id_pengeluaran')->nullable();
        });
    }

    public function down()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropColumn('id_pengeluaran');
            $table->unsignedBigInteger('id_pengeluaran')->nullable();

            // Kalau sebelumnya memang ada foreign key, bisa tambahkan kembali di sini
            $table->foreign('id_pengeluaran')->references('id')->on('pengeluaran')->onDelete('cascade');
        });
    }
};
