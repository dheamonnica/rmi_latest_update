<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('is_backdate')->default(0);
            $table->integer('product_id')->unsigned()->index();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        //product_id 
        //is_backdate
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['is_backdate', 'product_id']);
       });
    }
}
