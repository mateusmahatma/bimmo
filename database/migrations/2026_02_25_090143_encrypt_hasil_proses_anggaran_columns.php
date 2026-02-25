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
        // 1. Change schema to text
        Schema::table('hasil_proses_anggaran', function (Blueprint $table) {
            $table->text('nominal_anggaran')->nullable()->change();
            $table->text('anggaran_yang_digunakan')->nullable()->change();
            $table->text('sisa_anggaran')->nullable()->change();
        });

        // 2. Encrypt existing data
        $rows = DB::table('hasil_proses_anggaran')->get();
        foreach ($rows as $row) {
            DB::table('hasil_proses_anggaran')
                ->where('id_proses_anggaran', $row->id_proses_anggaran)
                ->update([
                'nominal_anggaran' => Crypt::encryptString((string)$row->nominal_anggaran),
                'anggaran_yang_digunakan' => Crypt::encryptString((string)$row->anggaran_yang_digunakan),
                'sisa_anggaran' => Crypt::encryptString((string)$row->sisa_anggaran),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Decrypt existing data
        $rows = DB::table('hasil_proses_anggaran')->get();
        foreach ($rows as $row) {
            try {
                DB::table('hasil_proses_anggaran')
                    ->where('id_proses_anggaran', $row->id_proses_anggaran)
                    ->update([
                    'nominal_anggaran' => Crypt::decryptString($row->nominal_anggaran),
                    'anggaran_yang_digunakan' => Crypt::decryptString($row->anggaran_yang_digunakan),
                    'sisa_anggaran' => Crypt::decryptString($row->sisa_anggaran),
                ]);
            }
            catch (\Exception $e) {
            // If decryption fails, it might be already decrypted or invalid
            }
        }

        // 2. Change schema back to decimal
        Schema::table('hasil_proses_anggaran', function (Blueprint $table) {
            $table->decimal('nominal_anggaran', 15, 2)->nullable()->change();
            $table->decimal('anggaran_yang_digunakan', 15, 2)->nullable()->change();
            $table->decimal('sisa_anggaran', 15, 2)->nullable()->change();
        });
    }
};
