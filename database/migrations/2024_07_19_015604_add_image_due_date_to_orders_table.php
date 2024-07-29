<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageDueDateToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('confirmed_good_image')->nullable();
            $table->string('confirmed_shipping_image')->nullable();
            $table->string('confirmed_delivered_image')->nullable();
            $table->string('digital_sign_image')->nullable();
            $table->text('hash_sign')->nullable();
            $table->string('due_date_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['confirmed_good_image', 'confirmed_shipping_image', 'confirmed_delivered_image', 'digital_sign_image', 'hash_sign', 'due_date_payment']);
        });
    }
}