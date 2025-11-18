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
            $table->unsignedBigInteger('subscriber_id');
            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('type_id');
            $table->integer('paid')->default(0);
            $table->boolean('invoiced')->default(true);
            $table->integer('cost');
            $table->string('patient_name');
            $table->date('receive');
            $table->date('delivery')->nullable();
            $table->string('patient_id');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'cancelled']);
            $table->index(['doctor_id', 'patient_id', 'created_at']);
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
