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

class StockTransfer extends Inspectable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Taggable, Imageable, Searchable, Feedbackable;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stock_transfers';

	 /**
     * The attributes that should be inspectable for restricted keywords.
     *
     * @var array
     */
    protected static $inspectable = [
        // 'name',
        // 'description',
    ];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'movement_number',
        'shop_depature_id',
        'transfer_type',
        'status',
        'shop_arrival_id',
        'transfer_date',
        'send_by_warehouse_time',
        'packed_time',
        'on_delivery_time',
        'delivered_time',
        'received_time',
        'approved_by_time',
        'packed_by',
        'transfer_by',
        'send_by_warehouse',
        'on_delivery_by',
        'delivered_by',
        'received_by',
        'created_by',
        'updated_by',
        'approved_by',

	];

    public function onDeliveredBy()
    {
        return $this->belongsTo(User::class, 'on_delivery_by');
    }

    public function sendByWarehouse()
    {
        return $this->belongsTo(User::class, 'send_by_warehouse');
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function packedBy()
    {
        return $this->belongsTo(User::class, 'packedBy');
    }

    public function trasferBy()
    {
        return $this->belongsTo(User::class, 'transfer_by');
    }

    /**
     * Get the items associated with the order.
     */
    public function items()
    {
        return $this->hasMany(StockTransferItem::class, 'stock_transfer_id');
    }

    /**
     * Get the inventories "from" the transfer through the items.
     */
    public function inventoriesFrom()
    {
        return $this->hasManyThrough(Inventory::class, StockTransferItem::class, 'stock_transfer_id', 'id', 'id', 'from_inventory_id');
    }

    /**
     * Get the inventories "to" the transfer through the items.
     */
    public function inventoriesTo()
    {
        return $this->hasManyThrough(Inventory::class, StockTransferItem::class, 'stock_transfer_id', 'id', 'id', 'to_inventory_id');
    }

    public function fromWarehouse()
    { 
        return $this->hasOne(Shop::class,'id','shop_depature_id');
    }

    public function toWarehouse()
    {
        return $this->hasOne(Shop::class,'id','shop_arrival_id');
    }
}