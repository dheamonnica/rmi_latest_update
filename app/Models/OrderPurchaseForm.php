<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderPurchaseForm extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'form_purchase_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'manufacture_id',
        'product_id',
        'inventory_id',
        'po_number_ref',
        'sku',
        'kode_reg_alkes',
        'hs_code',
        'product_name',
        'order_qty',
        'price_pcs',
        'subtotal',
        'shipping_fee',
        'total',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function getProduct() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
