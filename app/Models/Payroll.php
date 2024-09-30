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
        $query = "WITH RECURSIVE year_months AS (
            -- Base case: Start with the current year and January
            SELECT 1 AS month_num, YEAR(CURRENT_DATE()) AS year
            UNION ALL
            -- Recursive case: Increment the month and year
            SELECT 
                CASE WHEN month_num < 12 THEN month_num + 1 ELSE 1 END AS month_num,
                CASE WHEN month_num < 12 THEN year ELSE year + 1 END AS year
            FROM year_months
            WHERE year < 2500 -- Limit the recursion to the year 2300
        )
        select  MONTHNAME(STR_TO_DATE(ym.month_num, '%m')) AS month_name,
            ym.year, id id,name full_name, position, organization, basic_sallary,position_allowance, transportation, uang_oprational_harian,child_education_allowance, bonus_penjualan,bonus, overtime, reimburse_etoll_bensin,reimburse_pengobatan_sakit,tax_allowance, basic_sallary + position_allowance + transportation + uang_oprational_harian + child_education_allowance + bonus_penjualan + bonus  + overtime + reimburse_etoll_bensin + reimburse_pengobatan_sakit + tax_allowance total_allowance ,
        potongan_keterlambatan,potongan_alpha,potongan_absensi,pinjaman,cicilan,kasbon,jaminan_pensiun_employee,JHT_employee,PPH21, 
        potongan_keterlambatan+potongan_alpha+potongan_absensi+pinjaman+cicilan+kasbon+jaminan_pensiun_employee+JHT_employee + PPH21 total_deduction ,0 PPH21_payment,
        (basic_sallary + position_allowance + transportation + uang_oprational_harian + child_education_allowance + bonus_penjualan + bonus  + overtime + reimburse_etoll_bensin + reimburse_pengobatan_sakit + tax_allowance)  - (potongan_keterlambatan+potongan_alpha+potongan_absensi+pinjaman+cicilan+kasbon+jaminan_pensiun_employee+JHT_employee + PPH21) as take_home_pay,quota telekomunikasi
        from (SELECT u.id,name,p.position,null as organization,basic_sallary,position_allowance,transportation,operational_allowance * 26 as uang_oprational_harian,child_education_allowance,0 bonus_penjualan, 0 bonus,0 overtime,0 reimburse_etoll_bensin,0 reimburse_pengobatan_sakit, 0 tax_allowance, 0 potongan_keterlambatan, 0 potongan_alpha,0 potongan_absensi,0 pinjaman,0 cicilan, 0 kasbon,0 jaminan_pensiun_employee, 0 JHT_employee,0 PPH21 ,quota FROM payrolls p
        left join users u on p.level=u.level WHERE p.deleted_at IS NULL) aa
        CROSS JOIN year_months ym
        where ym.year = YEAR(CURRENT_DATE()) and ym.month_num <= MONTH(CURRENT_DATE())
        ORDER BY ym.year, ym.month_num;";

        return DB::select(DB::raw($query));
    }

}
