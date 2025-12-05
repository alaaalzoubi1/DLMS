<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lab_invoice_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id')->unique();
            $table->string('lab_name_ar');
            $table->string('lab_name_en');
            $table->string('address_ar');
            $table->string('address_en');
            $table->string('logo');
            $table->timestamps();

            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_invoice_headers');
    }

};
