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
        Schema::create('subscriber_zatca_credentials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscriber_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('private_key');
            $table->longText('csr');
            $table->longText('binary_security_token');

            $table->string('secret');
            $table->enum('environment', ['sandbox', 'simulation', 'production'])
                ->default('sandbox');

            $table->string('last_invoice_hash');
            $table->unsignedBigInteger('last_icv')->default(0);
            $table->date('certificate_expiry_date');
            $table->unique(['subscriber_id', 'environment']);

            $table->timestamp('onboarded_at')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_zatca_credentials');
    }
};
