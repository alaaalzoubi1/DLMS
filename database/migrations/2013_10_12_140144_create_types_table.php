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
        Schema::create('types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscriber_id')
                ->constrained()
                ->cascadeOnDelete()
                ->index();

            $table->enum('type', ['futures', 'new', 'test', 'returned']);
            $table->boolean('invoiced')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('types');
    }

};
