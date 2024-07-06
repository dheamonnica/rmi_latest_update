<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Target extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'target';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'hospital_name',
        'grand_total',
        'actual_sales',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function getCreatedTargetByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedTargetByName() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
