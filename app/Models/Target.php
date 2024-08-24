<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Target extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'target';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'month',
        'year',
        'shop_id',
        'hospital_id',
        'grand_total',
        'actual_sales',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function getCreatedTargetByName()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedTargetByName()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getWarehouse()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function getHospitalName()
    {
        return $this->belongsTo(Customer::class, 'hospital_id');
    }

    public static function getReportData()
    {
        $query = "
           select month, year, shop_id,s.name as warehouse_name, c.name as client_name,SUM(actual_sales) as actual_sales, sum(grand_total) as total_target
            from(
            SELECT * FROM `target`
            union all
            SELECT null,MONTHNAME(cast(created_at as date)) as order_month,YEAR(cast(created_at as date)) as order_year,
            shop_id, null , grand_total as actual_sales, customer_id,null,null,null,null,null,null
            FROM `orders` 
            where deleted_at + cancel_by IS NULL)report
            LEFT join shops s on report.shop_id=s.id
            LEFT join customers c on report.hospital_id=c.id
            group by month, shop_id, report.hospital_id, c.id, year
            ORDER BY CASE month
            WHEN 'January' THEN 1
            WHEN 'February' THEN 2
            WHEN 'March' THEN 3
            WHEN 'April' THEN 4
            WHEN 'May' THEN 5
            WHEN 'June' THEN 6
            WHEN 'July' THEN 7
            WHEN 'August' THEN 8
            WHEN 'September' THEN 9
            WHEN 'October' THEN 10
            WHEN 'November' THEN 11
            WHEN 'December' THEN 12
        END;
        ";

        return DB::select(DB::raw($query));
    }

    public static function getReportHeaderData()
    {
        $query = "
           select month, year, shop_id,s.name,sum(grand_total) as total_target,SUM(actual_sales) as actual_sales
            from(
            SELECT * FROM `target`
            union all
            SELECT null,MONTHNAME(cast(created_at as date)) as order_month,YEAR(cast(created_at as date)) as order_year,
            shop_id, null , grand_total as actual_sales, customer_id,null,null,null,null,null,null
            FROM `orders` 
            where deleted_at + cancel_by IS NULL)report
            LEFT join shops s on report.shop_id=s.id
            group by month, shop_id, year
            ORDER BY CASE month
            WHEN 'January' THEN 1
            WHEN 'February' THEN 2
            WHEN 'March' THEN 3
            WHEN 'April' THEN 4
            WHEN 'May' THEN 5
            WHEN 'June' THEN 6
            WHEN 'July' THEN 7
            WHEN 'August' THEN 8
            WHEN 'September' THEN 9
            WHEN 'October' THEN 10
            WHEN 'November' THEN 11
            WHEN 'December' THEN 12
        END;
        ";

        return DB::select(DB::raw($query));
    }

    public static function getReportDataHeaderAdministrator()
    {
        $query = "
           select month, year, SUM(actual_sales) as actual_sales, sum(grand_total) as total_target
            from(
            SELECT * FROM `target`
            union all
            SELECT null,MONTHNAME(cast(created_at as date)) as order_month,YEAR(cast(created_at as date)) as order_year,
            shop_id, null , grand_total as actual_sales, customer_id,null,null,null,null,null,null
            FROM `orders` 
            where deleted_at + cancel_by IS NULL) report
            group by month, year
            ORDER BY CASE 
            WHEN month = 'January' THEN 1
            WHEN month = 'February' THEN 2
            WHEN month = 'March' THEN 3
            WHEN month = 'April' THEN 4
            WHEN month = 'May' THEN 5
            WHEN month = 'June' THEN 6
            WHEN month = 'July' THEN 7
            WHEN month = 'August' THEN 8
            WHEN month = 'September' THEN 9
            WHEN month = 'October' THEN 10
            WHEN month = 'November' THEN 11
            WHEN month = 'December' THEN 12 END;
        ";

        return DB::select(DB::raw($query));
    }

    public static function getReportDataExpandAdministrator()
    {
        $query = "
           select month, year, shop_id,s.name as warehouse_name,
           SUM(actual_sales) as actual_sales, sum(grand_total) as total_target
            from(
            SELECT * FROM `target`
            union all
            SELECT null,MONTHNAME(cast(created_at as date)) as order_month,YEAR(cast(created_at as date)) as order_year,
            shop_id, null , grand_total as actual_sales, customer_id,null,null,null,null,null,null
            FROM `orders` 
            where deleted_at + cancel_by IS NULL)report
            LEFT join shops s on report.shop_id=s.id
            group by month, shop_id, year
            ORDER BY CASE month
            WHEN 'January' THEN 1
            WHEN 'February' THEN 2
            WHEN 'March' THEN 3
            WHEN 'April' THEN 4
            WHEN 'May' THEN 5
            WHEN 'June' THEN 6
            WHEN 'July' THEN 7
            WHEN 'August' THEN 8
            WHEN 'September' THEN 9
            WHEN 'October' THEN 10
            WHEN 'November' THEN 11
            WHEN 'December' THEN 12
        END;
        ";

        return DB::select(DB::raw($query));
    }

    public static function getReportDataExpandClientAdministrator()
    {
        $query = "
           select month, year, shop_id,s.name as warehouse_name, c.name as client_name,SUM(actual_sales) as actual_sales, sum(grand_total) as total_target
            from(
            SELECT * FROM `target`
            union all
            SELECT null,MONTHNAME(cast(created_at as date)) as order_month,YEAR(cast(created_at as date)) as order_year,
            shop_id, null , grand_total as actual_sales, customer_id,null,null,null,null,null,null
            FROM `orders` 
            where deleted_at + cancel_by IS NULL)report
            LEFT join shops s on report.shop_id=s.id
            LEFT join customers c on report.hospital_id=c.id
            group by month, shop_id, year
            ORDER BY CASE month
            WHEN 'January' THEN 1
            WHEN 'February' THEN 2
            WHEN 'March' THEN 3
            WHEN 'April' THEN 4
            WHEN 'May' THEN 5
            WHEN 'June' THEN 6
            WHEN 'July' THEN 7
            WHEN 'August' THEN 8
            WHEN 'September' THEN 9
            WHEN 'October' THEN 10
            WHEN 'November' THEN 11
            WHEN 'December' THEN 12
        END;
        ";

        return DB::select(DB::raw($query));
    }
}
