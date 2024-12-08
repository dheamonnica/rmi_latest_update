<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasingOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_receiver_id');
            $table->integer('shop_requester_id');
            $table->integer('stock_transfer_id')->nullable();
            $table->string('purchasing_invoice_number');
            $table->date('purchasing_date');
            $table->integer('request_by')->nullable();
            $table->timestamp('request_at')->nullable();
            $table->integer('in_progress_by')->nullable();
            $table->timestamp('in_progress_at')->nullable();
            $table->integer('shipped_by')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->integer('depatured_by')->nullable();
            $table->timestamp('depatured_at')->nullable();
            $table->integer('arrival_by')->nullable();
            $table->timestamp('arrival_at')->nullable();
            $table->integer('transfered_stock_by')->nullable();
            $table->timestamp('transfered_stock_at')->nullable();
            $table->integer('transfered_completed_by')->nullable();
            $table->timestamp('transfered_completed_at')->nullable();
            $table->integer('done_by')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->integer('shipment_status');
            $table->integer('transfer_status');
            $table->integer('request_status');
            $table->timestamps();
        });

        Schema::create('purchasing_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('purchasing_order_id');
            $table->integer('inventory_id');
            $table->integer('manufacture_id');
            $table->integer('stock_transfer_id')->nullable();
            $table->integer('request_quantity');
            $table->string('manufacture_number');
            $table->string('price');
            $table->integer('depatured_by')->nullable();
            $table->timestamp('depatured_at')->nullable();
            $table->integer('arrival_by')->nullable();
            $table->timestamp('arrival_at')->nullable();
            $table->integer('fulfilled_by')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->integer('shipment_status');
            $table->integer('transfer_status');
            $table->integer('request_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchasing_orders');
    }
}
