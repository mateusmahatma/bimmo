<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            // Hapus kolom dulu, supaya bisa ditambahkan ulang
            if (Schema::hasColumn('anggaran', 'id_pengeluaran')) {
                $table->dropColumn('id_pengeluaran');
            }
        });

        Schema::table('anggaran', function (Blueprint $table) {
            // Tambahkan kembali sebagai text
            $table->text('id_pengeluaran')->nullable();
        });
    }

    public function down()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropColumn('id_pengeluaran');
            $table->unsignedBigInteger('id_pengeluaran')->nullable(); // atau tipe lama kamu
        });
    }
};
