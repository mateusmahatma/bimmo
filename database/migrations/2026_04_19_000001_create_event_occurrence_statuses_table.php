<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_occurrence_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->dateTime('occurrence_start');
            $table->string('status')->default('completed'); // completed
            $table->timestamps();

            $table->unique(['event_id', 'id_user', 'occurrence_start'], 'evt_occ_unique');
            $table->index(['id_user', 'occurrence_start'], 'evt_occ_user_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_occurrence_statuses');
    }
};

