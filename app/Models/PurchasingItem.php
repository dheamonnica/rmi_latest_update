<?php

namespace App\Models;

use App\Common\CascadeSoftDeletes;
use App\Common\Feedbackable;
use App\Common\Imageable;
use App\Common\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Laravel\Scout\Searchable;

class PurchasingItem extends Inspectable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Taggable, Imageable, Searchable, Feedbackable;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchasing_order_items';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchasing_order_id',
        'inventory_id',
        'product_id',
        'manufacture_id',
        'stock_transfer_id',
        'request_quantity',
        'manufacture_number',
        'price',
        'depatured_by',
        'depatured_at',
        'arrival_by',
        'arrival_at',
        'fulfilled_by',
        'fulfilled_at',
        'shipment_status',
        'transfer_status',
        'request_status',
	];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

	public function stockTransfer() {
		return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
	}

	public function manufacture() {
		return $this->belongsTo(Manufacturer::class, 'manufacture_id');
	}

	public function inventory()
	{
		return $this->belongsTo(Inventory::class, 'inventory_id');
	}

	public function purchasing()
	{
		return $this->belongsTo(Purchasing::class, 'purchasing_order_id');
	}

	public function depaturedBy()
    {
        return $this->belongsTo(User::class, 'depatured_by');
    }

	public function arrivalBy()
    {
        return $this->belongsTo(User::class, 'arrival_by');
    }

	public function fulfilledBy()
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }
}