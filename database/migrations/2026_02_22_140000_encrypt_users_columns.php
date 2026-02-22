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
        // First drop unique constraints
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['no_hp']);
        });

        // Then change column types and add hash columns
        Schema::table('users', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->text('no_hp')->nullable()->change();
            $table->text('nominal_target_dana_darurat')->nullable()->change();

            $table->string('email_hash')->nullable()->after('email');
            $table->string('no_hp_hash')->nullable()->after('no_hp');
        });

        // Finally add new unique constraints on hash columns
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email_hash');
            $table->unique('no_hp_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email_hash']);
            $table->dropUnique(['no_hp_hash']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('no_hp')->nullable()->change();
            $table->decimal('nominal_target_dana_darurat', 15, 2)->nullable()->change();

            $table->dropColumn(['email_hash', 'no_hp_hash']);

            $table->unique('email');
            $table->unique('no_hp');
        });
    }
};
