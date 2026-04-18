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
        Schema::create('order_product_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('subscriber_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('specialization_name');

            $table->timestamps();

            $table->index(['subscriber_id', 'user_id', 'created_at']);
            $table->index(['order_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_histories');
    }
};
