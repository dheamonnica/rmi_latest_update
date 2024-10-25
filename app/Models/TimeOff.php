<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeOff extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timeoffs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_at',
        'created_by',
        'start_date',
        'end_date',
        'category',
        'total_days',
        'type',
        'notes',
        'status',
        'approved_at',
        'approved_by',
        'updated_at',
        'updated_by',
    ];

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

    public static function getUserTimeOffAnnualLeave($user_id) {
        return DB::table('timeoffs')
        ->selectRaw('*, SUM(total_days) as sum_total_days')
        ->where('created_by', $user_id)
        ->where('category', 'annual_leave')
        ->whereYear('created_at', Carbon::now()->year)
        ->whereNull('deleted_at')
        ->first();
    }

    public static function getUserTimeOffSickLeave($user_id) {
        return DB::table('timeoffs')
        ->selectRaw('*, SUM(total_days) as sum_total_days')
        ->where('created_by', $user_id)
        ->where('category', 'sick_leave')
        ->whereYear('created_at', Carbon::now()->year)
        ->whereNull('deleted_at')
        ->first();
    }
}
