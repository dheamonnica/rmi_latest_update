<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Budget extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'budget';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'shop_id',
        'requirement',
        'qty',
        'total',
        'grand_total',
        'category',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function getCreatedBudgetByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedBudgetByName() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
