<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Offering extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offering';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'small_quantity_price',
        'medium_quantity_price',
        'large_quantity_price',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
        'status',
    ];

    public function getProductName() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getCreatedOfferedByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedOfferedByName() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getDatabyUser($user_id)
    {
        return DB::table('offering')->where('created_by', $user_id)->get();
    }
}
