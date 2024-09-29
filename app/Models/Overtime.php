<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Overtime extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'overtime_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'start_time',
        'end_time',
        'status',
        'approved_at',
        'approved_by',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'

    ];

    public function getCreatedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedUsername()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedUsername()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getApprovedUsername()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function updateStatusApprove(Request $request, $overtime)
    {
        $overtime->approved_at = date("Y-m-d G:i:s");
        $overtime->approved_by = Auth::user()->id;
        $overtime->updated_at = date("Y-m-d G:i:s");
        $overtime->updated_by = Auth::user()->id;
        $overtime->status = 1;

        return $overtime->save();
    }
}
