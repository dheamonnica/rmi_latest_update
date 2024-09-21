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
        'basic_sallary',
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

    public static function getReportPayroll()
    {
        $query = "select id id,name full_name, position, organization, basic_sallary,position_allowance, transportation, operational_allowance,child_education_allowance, sales_bonus,bonus, overtime, reimburse_e_toll_gasoline,medical_reimbursement,tax_allowance, basic_sallary + position_allowance + transportation + operational_allowance + child_education_allowance + sales_bonus + bonus  + overtime + reimburse_e_toll_gasoline + medical_reimbursement + tax_allowance total_allowance ,
        lateness_deduction,alpha_deduction,absence_deduction,loan,installment,kasbon,employee_pension_security,employee_jht,pph_21, 
        lateness_deduction+alpha_deduction+absence_deduction+loan+installment+kasbon+employee_pension_security+employee_jht + pph_21 total_deduction ,0 pph_21_payment,
        (basic_sallary + position_allowance + transportation + operational_allowance + child_education_allowance + sales_bonus + bonus  + overtime + reimburse_e_toll_gasoline + medical_reimbursement + tax_allowance)  - (lateness_deduction+alpha_deduction+absence_deduction+loan+installment+kasbon+employee_pension_security+employee_jht + pph_21) as take_home_pay,quota telecommunication_allowance
        from (SELECT u.id,name,p.position,null as organization,basic_sallary,position_allowance,transportation,operational_allowance * 26 as operational_allowance,child_education_allowance,0 sales_bonus, 0 bonus,0 overtime,0 reimburse_e_toll_gasoline,0 medical_reimbursement, 0 tax_allowance, 0 lateness_deduction, 0 alpha_deduction,0 absence_deduction,0 loan,0 installment, 0 kasbon,0 employee_pension_security, 0 employee_jht,0 pph_21 ,quota FROM payrolls p
        left join users u on p.level=u.level) aa;";

        return DB::select(DB::raw($query));
    }

}
