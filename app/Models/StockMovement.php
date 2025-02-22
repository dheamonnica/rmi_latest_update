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

class StockMovement extends Inspectable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Taggable, Imageable, Searchable, Feedbackable;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stock_movement';

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
        'movement_timestamp',
        'inventory_id',
        'product_id',
        'shop_id',
        'qty',
        'initial_qty',
        'status',
        'source',
        'admin_note',
        'transfer_by',
        'created_by',
        'updated_by',
	];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function shopFrom()
    {
        return $this->belongsTo(Shop::class, 'shop_depature_id');
    }

    public function shopTo()
    {
        return $this->belongsTo(Shop::class, 'shop_arrival_id');
    }

    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}