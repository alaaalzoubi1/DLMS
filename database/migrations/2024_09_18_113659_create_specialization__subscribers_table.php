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
        Schema::create('specialization__subscribers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specializations_id');
            $table->unsignedBigInteger('subscriber_id');

            $table->foreign('specializations_id')->references('id')->on('specializations')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->index(['id', 'subscriber_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialization__subscribers');
    }
};
