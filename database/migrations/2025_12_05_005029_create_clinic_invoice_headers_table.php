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
        Schema::create('clinic_invoice_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id')->unique();
            $table->string('clinic_name_ar');
            $table->string('clinic_name_en');
            $table->string('address');
            $table->string('logo');
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinic_invoice_headers');
    }

};
