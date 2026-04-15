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
        Schema::create('subscriber_doctor_price_sittings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_account_id');
            $table->unsignedBigInteger('subscriber_id');

            $table->boolean('hide_prices')->default(false);
            $table->boolean('hide_financial_stats')->default(false);

            $table->timestamps();

            $table->unique(['doctor_account_id', 'subscriber_id'],'doc_acc_sub_price_unique');

            $table->foreign('doctor_account_id')
                ->references('id')
                ->on('doctor__accounts')
                ->onDelete('cascade');

            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_doctor_price_sittings');
    }
};
