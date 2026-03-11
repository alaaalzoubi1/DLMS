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
        Schema::create('zatca_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->uuid()->unique();
            $table->unsignedBigInteger('icv')->nullable();
            $table->string('previous_invoice_hash')->nullable();
            $table->string('invoice_hash')->nullable();
            $table->text('qr_code')->nullable();
            $table->string('cleared_invoice')->nullable();
            $table->json('info_messages')->nullable();
            $table->json('error_messages')->nullable();
            $table->json('warning_messages')->nullable();
            $table->enum('clearance_status', [
                'PENDING',
                'REPORTED',
                'CLEARED',
                'NOT_CLEARED',
                'NOT_REPORTED'
            ])->default('PENDING');
            $table->string('zatca_invoice_number')->nullable();
            $table->string('invoice_type')->nullable();
            $table->unsignedSmallInteger('zatca_http_status')->nullable();
            $table->longText('request_payload')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('total_vat_amount', 15, 2)->nullable();
            $table->decimal('total_net_amount', 15, 2)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zatca_documents');
    }
};
