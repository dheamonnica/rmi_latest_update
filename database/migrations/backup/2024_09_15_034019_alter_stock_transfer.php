<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStockTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number')->nullable();
            $table->integer('shop_depature_id')->nullable();
            $table->string('transfer_type')->nullable();
            $table->string('status')->nullable(); //default packing
            $table->integer('shop_arrival_id')->nullable();
            $table->date('transfer_date');
            $table->timestamp('packed_time');
            $table->timestamp('delivered_time');
            $table->timestamp('received_time');
            $table->integer('transfer_by')->nullable();
            $table->integer('delivered_by')->nullable();
            $table->integer('received_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
}
