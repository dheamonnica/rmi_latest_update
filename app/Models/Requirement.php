<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Requirement extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'requirements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'warehouse_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function getCreatedUsername() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getWarehouse() {
        return $this->belongsTo(Shop::class, 'warehouse_id');
    }

    public function getUpdatedUsername() {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
