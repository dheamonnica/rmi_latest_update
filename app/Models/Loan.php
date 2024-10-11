<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Loan extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'loan_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_at',
        'created_by',
        'status',
        'amount',
        'reason',
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getLoanAndPaymentData($id) {
        $query = "SELECT 
            loan_users.created_by, 
            SUM(loan_users.amount) AS sum_amount_loan, 
            SUM(loan_payment_users.amount) over (PARTITION by loan_payment_users.created_by ) AS sum_amount_loan_payment
        FROM `loan_users` left JOIN `loan_payment_users` 
        ON `loan_users`.`created_by` = `loan_payment_users`.`user_id`
        AND loan_payment_users.deleted_at IS NULL
        WHERE loan_users.deleted_at IS NULL AND loan_users.status = 1 AND loan_users.created_by = $id";

        return DB::select(DB::raw($query));
    }
}
