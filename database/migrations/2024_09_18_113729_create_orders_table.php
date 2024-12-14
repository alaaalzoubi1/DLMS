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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('specialization_users_id');
            $table->enum('status', ['pending', 'completed', 'cancelled']);
            $table->enum('type', ['futures', 'new', 'test','returned']);
            $table->boolean('invoiced')->default(true);
            $table->integer('paid')->default(0);
            $table->integer('cost');
            $table->string('patient_name');
            $table->date('receive');
            $table->date('delivery')->nullable();
            $table->string('patient_id');
            $table->string('specialization');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('specialization_users_id')->references('id')->on('specialization__users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
