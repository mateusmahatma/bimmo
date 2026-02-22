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
            $table->string('name_hash')->nullable()->after('name');
        });

        // Drop current unique on name if it exists (it might be a string index)
        // Note: 'name' is now TEXT, so unique on it would fail anyway if not dropped.
        // But since I changed it to TEXT in 2026_02_22_140000, the index might have been dropped or it failed.
        // Actually, change() usually tries to keep indexes unless dropped.

        Schema::table('users', function (Blueprint $table) {
            $table->unique('name_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['name_hash']);
            $table->dropColumn('name_hash');
        });
    }
};
