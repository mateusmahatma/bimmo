<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anggaran') && !Schema::hasColumn('anggaran', 'id_periode_anggaran')) {
            Schema::table('anggaran', function (Blueprint $table) {
                $table->unsignedBigInteger('id_periode_anggaran')->nullable()->after('id_user');
                $table->index('id_periode_anggaran');
                $table->foreign('id_periode_anggaran')
                    ->references('id_periode_anggaran')
                    ->on('periode_anggaran')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('hasil_proses_anggaran') && !Schema::hasColumn('hasil_proses_anggaran', 'id_periode_anggaran')) {
            Schema::table('hasil_proses_anggaran', function (Blueprint $table) {
                $table->unsignedBigInteger('id_periode_anggaran')->nullable()->after('id_user');
                $table->index('id_periode_anggaran');
                $table->foreign('id_periode_anggaran')
                    ->references('id_periode_anggaran')
                    ->on('periode_anggaran')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hasil_proses_anggaran') && Schema::hasColumn('hasil_proses_anggaran', 'id_periode_anggaran')) {
            Schema::table('hasil_proses_anggaran', function (Blueprint $table) {
                $table->dropForeign(['id_periode_anggaran']);
                $table->dropIndex(['id_periode_anggaran']);
                $table->dropColumn('id_periode_anggaran');
            });
        }

        if (Schema::hasTable('anggaran') && Schema::hasColumn('anggaran', 'id_periode_anggaran')) {
            Schema::table('anggaran', function (Blueprint $table) {
                $table->dropForeign(['id_periode_anggaran']);
                $table->dropIndex(['id_periode_anggaran']);
                $table->dropColumn('id_periode_anggaran');
            });
        }
    }
};

