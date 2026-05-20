<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('alert_cashflow_deficit_enabled')->default(true);
            $table->boolean('alert_debt_service_ratio_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'alert_cashflow_deficit_enabled',
                'alert_debt_service_ratio_enabled',
            ]);
        });
    }
};
