<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_movement', function (Blueprint $table) {
            $table->id();
            $table->date('transfer_date');
            $table->integer('product_id')->nullable();
            $table->integer('shop_depature_id')->nullable();
            $table->string('transfer_type')->nullable();
            $table->string('status')->nullable();
            $table->integer('shop_arrival_id')->nullable();
            $table->integer('stock_qty')->nullable();
            $table->integer('transfer_by')->nullable();
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
        Schema::dropIfExists('stock_movement');
    }
}
