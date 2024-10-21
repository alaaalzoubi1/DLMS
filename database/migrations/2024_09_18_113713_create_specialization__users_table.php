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
            Schema::create('specialization__users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_specializations_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('subscriber_specializations_id')->references('id')->on('specialization__subscribers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialization__users');
    }
};
