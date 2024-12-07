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
        'clock_out',
        'branch_loc',
        'total_hours'
    ];
    
    public function getUsername()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getWarehouse()
    {
        return $this->belongsTo(Merchant::class, 'branch_loc');
    }
}
