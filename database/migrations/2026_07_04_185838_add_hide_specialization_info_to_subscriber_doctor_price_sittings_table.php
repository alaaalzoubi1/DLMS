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
        Schema::table('subscriber_doctor_price_sittings', function (Blueprint $table) {
            $table->boolean('hide_specialization_info')
                ->default(false)
                ->after('hide_financial_stats');
        });
    }

    public function down(): void
    {
        Schema::table('subscriber_doctor_price_sittings', function (Blueprint $table) {
            $table->dropColumn('hide_specialization_info');
        });
    }
};
