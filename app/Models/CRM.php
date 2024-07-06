<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CRM extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'crm';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'client_id',
        'verfified_by',
        'verified_at',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public function getCreatedCRMByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedCRMByName() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getClientByName() {
        return $this->belongsTo(Customer::class, 'client_id');
    }

    public function getVerifiedByName() {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
