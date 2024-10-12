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

    public static function getLoanAndPaymentData($id)
    {
        $query = "SELECT 
            users.name,
            loan_users.created_by,
            COALESCE(SUM(loan_users.amount), 0) AS sum_amount_loan,
            COALESCE((SELECT SUM(loan_payment_users.amount) 
                    FROM loan_payment_users 
                    WHERE loan_payment_users.user_id = $id 
                    AND loan_payment_users.deleted_at IS NULL), 0) AS sum_amount_loan_payment
        FROM loan_users
        LEFT JOIN users ON loan_users.created_by = users.id
        WHERE loan_users.created_by = $id 
        AND loan_users.status = 1;";

        return DB::select(DB::raw($query));
    }

    public static function getDataLoanReportFirst()
    {
        $query = "SELECT 
            users.name,
            loan_users.created_by as created_by_id,
            COALESCE((SELECT SUM(loan_users.amount) 
                      FROM loan_users 
                      WHERE loan_users.deleted_at IS NULL
                      AND loan_users.status = 1
                      AND loan_users.created_by = loan_payment_users.user_id), 0) 
                      AS sum_amount_loan,
            COALESCE((SELECT SUM(loan_payment_users.amount) 
                      FROM loan_payment_users 
                      WHERE loan_payment_users.deleted_at IS NULL
                      AND loan_payment_users.user_id = loan_users.created_by), 0) 
                      AS sum_amount_loan_payment
          FROM loan_users
          LEFT JOIN users ON loan_users.created_by = users.id
          LEFT JOIN loan_payment_users ON loan_payment_users.user_id = loan_users.created_by
          GROUP BY loan_users.created_by, users.name, loan_payment_users.user_id;";

        return DB::select(DB::raw($query));
    }

    public static function getDataLoanReportSecond()
    {
        $query = "SELECT loan_payment_users.*, 
        created_by_user.name as created_by_name, 
        updated_by_user.name as updated_by_name
        FROM loan_payment_users 
        LEFT JOIN users as created_by_user ON loan_payment_users.created_by = created_by_user.id
        LEFT JOIN users as updated_by_user ON loan_payment_users.updated_by = updated_by_user.id
        WHERE loan_payment_users.deleted_at IS NULL";

        return DB::select(DB::raw($query));
    }
}
