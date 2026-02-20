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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('metode_target_dana_darurat', ['manual', 'otomatis'])->default('otomatis')->after('password');
            $table->decimal('nominal_target_dana_darurat', 15, 2)->nullable()->after('metode_target_dana_darurat');
            $table->integer('kelipatan_target_dana_darurat')->default(6)->after('nominal_target_dana_darurat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['metode_target_dana_darurat', 'nominal_target_dana_darurat', 'kelipatan_target_dana_darurat']);
        });
    }
};
