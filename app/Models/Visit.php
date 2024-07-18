<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Visit extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'visit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'client_id',
        'shop_id',
        'assignee_user_id',
        'note',
        'next_visit_date',
        'status',
        'verfified_by',
        'verified_at',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public function getCreatedVisitByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedVisitByName() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getClientByName() {
        return $this->belongsTo(Customer::class, 'client_id');
    }

    public function getVerifiedByName() {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getAssigneeByName() {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function getWarehouseByShop() {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
