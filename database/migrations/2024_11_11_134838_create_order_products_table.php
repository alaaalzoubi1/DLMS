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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->text('note')->nullable();
            $table->json('tooth_numbers');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->double('unit_price');
            $table->string('product_name');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('tooth_color_id');
            $table->foreign('tooth_color_id')->references('id')->on('tooth_colors')->onDelete('cascade');
            $table->unsignedBigInteger('specialization_users_id');
            $table->foreign('specialization_users_id')->references('id')->on('specialization__users')->onDelete('cascade');
            $table->enum('status', ['working', 'finished'])->default('working');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
