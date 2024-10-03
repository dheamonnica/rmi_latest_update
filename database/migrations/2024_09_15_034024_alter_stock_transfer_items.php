<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStockTransferItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_transfer_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('from_inventory_id')->nullable();
            $table->integer('to_inventory_id')->nullable();
            $table->integer('before_depature_stock')->default(0);
            $table->integer('after_depature_stock')->default(0);
            $table->integer('before_arrival_stock')->default(0);
            $table->integer('after_arrival_stock')->default(0);
            $table->integer('transfer_qty')->default(0);
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
        Schema::dropIfExists('stock_transfer_items');
    }
}
