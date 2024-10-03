<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStockTransferAddStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {  
            $table->integer('send_by_warehouse')->nullable();
            $table->timestamp('send_by_warehouse_time');
            $table->integer('on_delivery_by')->nullable();
            $table->timestamp('on_delivery_time');
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_by_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn(['send_by_warehouse', 'send_by_warehouse_time', 'on_delivery_time', 'on_delivery_by', 'approved_by', 'approved_time']);
        });
    }
}
