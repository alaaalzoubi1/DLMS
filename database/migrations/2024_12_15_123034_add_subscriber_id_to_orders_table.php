<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriberIdToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
// إضافة عمود subscriber_id
            $table->unsignedBigInteger('subscriber_id')->after('id');

// إضافة العلاقة (foreign key)
            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
// حذف العلاقة والعمود
            $table->dropForeign(['subscriber_id']);
            $table->dropColumn('subscriber_id');
        });
    }
}
