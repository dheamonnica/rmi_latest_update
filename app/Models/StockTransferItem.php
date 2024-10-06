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

class StockTransferItem extends Inspectable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Taggable, Imageable, Searchable, Feedbackable;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stock_transfer_items';

	 /**
     * The attributes that should be inspectable for restricted keywords.
     *
     * @var array
     */
    protected static $inspectable = [

    ];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'from_inventory_id',
        'to_inventory_id',
        'before_depature_stock',
        'after_depature_stock',
        'before_arrival_stock',
        'after_arrival_stock',
        'transfer_qty',
        'created_by',
        'updated_by',
	];

     /**
     * Get the "from" inventory for the item.
     */
    public function fromInventory()
    {
        return $this->belongsTo(Inventory::class, 'from_inventory_id');
    }

    /**
     * Get the "to" inventory for the item.
     */
    public function toInventory()
    {
        return $this->belongsTo(Inventory::class, 'to_inventory_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}