<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Payroll extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payrolls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'position',
        'grade',
        'sub_grade',
        'level',
        'take_home_pay',
        'basic_salary',
        'operational_allowance',
        'position_allowance',
        'child_education_allowance',
        'transportation',
        'quota',
        'created_at',
        'created_by',
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

}
