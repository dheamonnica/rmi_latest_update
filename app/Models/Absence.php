<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Absence extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'absence';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'longitude',
        'latitude',
        'address',
        'clock_in',
        'clock_in_img',
        'clock_out',
        'clock_out_img',
        'branch_loc',
        'warehouse_id',
        'total_hours'
    ];
    
    public function getUsername()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getWarehouse()
    {
        return $this->belongsTo(Shop::class, 'warehouse_id');
    }
}
