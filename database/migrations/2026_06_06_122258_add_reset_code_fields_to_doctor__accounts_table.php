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
        Schema::table('doctor__accounts', function (Blueprint $table) {
            $table->string('reset_code')->nullable()->after('password');
            $table->timestamp('reset_expires_at')->nullable()->after('reset_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor__accounts', function (Blueprint $table) {
            //
        });
    }
};
