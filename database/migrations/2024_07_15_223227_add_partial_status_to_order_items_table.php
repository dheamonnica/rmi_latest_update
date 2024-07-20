<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartialStatusToOrderItemsTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // if (!Schema::hasColumn('order_items', 'fulfilment_type')) {
        //     Schema::table('order_items', function (Blueprint $table) {
        //         $table->integer('partial_status')->default(0);
        //     });
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // if (Schema::hasColumn('order_items', 'payment_instruction')) {
        //     Schema::table('order_items', function (Blueprint $table) {
        //         $table->dropColumn('is_partial');
        //     });
        // }
    }
}
