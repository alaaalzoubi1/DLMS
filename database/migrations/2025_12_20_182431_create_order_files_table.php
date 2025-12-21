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
        Schema::create('order_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('file_path');
            $table->string('original_name');
            $table->string('extension', 10);
            $table->unsignedBigInteger('size');
            $table->string('mime_type')->nullable();

            $table->enum('status', ['pending', 'uploaded', 'failed'])
                ->default('pending');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_files');
    }
};
