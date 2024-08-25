<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Budget extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'budget';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'shop_id',
        'requirement',
        'qty',
        'total',
        'grand_total',
        'category',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'approved_at',
        'approved_by',
        'deleted_at',
        'deleted_by',
    ];

    public function getCreatedBudgetByName()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUpdatedBudgetByName()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getWarehouseByName()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function getApprovedBudget()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function getReportDataHeaderAdministrator()
    {
        $query = "select year,month,max(total_selling) as total_selling ,sum(total_budget) as total_budget,max(total_budget)/sum(total_selling) * 100 as rate_cost from (SELECT 
            year(o.created_at) year,
            monthname(o.created_at) month,
            o.shop_id,
            s.name AS warehouse_area,
            seg.name segment_name,
            SUM(o.grand_total) AS total_selling,
            seg.value Rate,
            SUM(o.grand_total * seg.value) /100 AS total_budget
            

        FROM 
            orders o
        left JOIN
            order_items oi on o.id=oi.order_id
        left JOIN 
            products p ON oi.product_id = p.id
        left JOIN 
            shops s ON o.shop_id = s.id 
        left join 
            segment seg on o.shop_id = seg.warehouse_id
        where seg.deleted_at is null    
        GROUP BY year(o.created_at),
            monthname(o.created_at),
            o.shop_id, s.name,
            seg.name, seg.value

        union all

        SELECT 
            year(o.created_at) year,
            monthname(o.created_at) month,
            o.shop_id,
            s.name AS warehouse_area,
            'buying product' segment_name,
            SUM(o.grand_total) AS total_selling,
            'Conditonal' Rate,
            SUM(oi.quantity * p.purchase_price) AS total_budget

        FROM 
            orders o
        left JOIN
            order_items oi on o.id=oi.order_id
        left JOIN 
            products p ON oi.product_id = p.id
        left JOIN 
            shops s ON o.shop_id = s.id 
            
        GROUP BY year(o.created_at),
            monthname(o.created_at),
            o.shop_id, s.name

        UNION all
        SELECT 
            year(b.date) year,
            monthname(b.date) month,
            b.shop_id,
            s.name AS warehouse_area,
            'Oprasional' segment_name,
            0 AS total_selling,
            'Conditonal' Rate,
            sum(grand_total) AS total_budget

        FROM 
            budget b
        left JOIN 
            shops s ON b.shop_id = s.id 
        WHERE status=1    
            
        GROUP BY year(b.date),
            monthname(b.date),
            b.shop_id, s.name) aa
            where warehouse_area is not null
                group by  year,month
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
        END";

        return DB::select(DB::raw($query));
    }

    public static function getReportDataExpandAdministrator()
    {
        $query = "select year,month,warehouse_area,max(total_selling) as total_selling ,sum(total_budget) as total_budget,max(total_budget)/sum(total_selling) * 100 as rate_cost from (SELECT 
            year(o.created_at) year,
            monthname(o.created_at) month,
            o.shop_id,
            s.name AS warehouse_area,
            seg.name segment_name,
            SUM(o.grand_total) AS total_selling,
            seg.value Rate,
            SUM(o.grand_total * seg.value) /100 AS total_budget
            

        FROM 
            orders o
        left JOIN
            order_items oi on o.id=oi.order_id
        left JOIN 
            products p ON oi.product_id = p.id
        left JOIN 
            shops s ON o.shop_id = s.id 
        left join 
            segment seg on o.shop_id = seg.warehouse_id
        where seg.deleted_at is null    
        GROUP BY year(o.created_at),
            monthname(o.created_at),
            o.shop_id, s.name,
            seg.name, seg.value

        union all

        SELECT 
            year(o.created_at) year,
            monthname(o.created_at) month,
            o.shop_id,
            s.name AS warehouse_area,
            'buying product' segment_name,
            SUM(o.grand_total) AS total_selling,
            'Conditonal' Rate,
            SUM(oi.quantity * p.purchase_price) AS total_budget

        FROM 
            orders o
        left JOIN
            order_items oi on o.id=oi.order_id
        left JOIN 
            products p ON oi.product_id = p.id
        left JOIN 
            shops s ON o.shop_id = s.id 
            
        GROUP BY year(o.created_at),
            monthname(o.created_at),
            o.shop_id, s.name

        UNION all
        SELECT 
            year(b.date) year,
            monthname(b.date) month,
            b.shop_id,
            s.name AS warehouse_area,
            'Oprasional' segment_name,
            0 AS total_selling,
            'Conditonal' Rate,
            sum(grand_total) AS total_budget

        FROM 
            budget b
        left JOIN 
            shops s ON b.shop_id = s.id 
        WHERE status=1    
            
        GROUP BY year(b.date),
            monthname(b.date),
            b.shop_id, s.name) aa
            where warehouse_area is not null
                group by  year,month,warehouse_area;";
        return DB::select(DB::raw($query));
    }

    public static function getReportDataExpandClientAdministrator()
    {
        $query = "select year,month,warehouse_area,segment_name,total_selling,rate as segment_rate,total_budget,total_budget/total_selling *100 as rate_cost from (
        SELECT 
            year(o.created_at) year,
            monthname(o.created_at) month,
            o.shop_id,
            s.name AS warehouse_area,
            seg.name segment_name,
            SUM(o.grand_total) AS total_selling,
            seg.value Rate,
            SUM(o.grand_total * seg.value) /100 AS total_budget

        FROM 
            orders o
        left JOIN
            order_items oi on o.id=oi.order_id
        left JOIN 
            products p ON oi.product_id = p.id
        left JOIN 
            shops s ON o.shop_id = s.id 
        left join 
            segment seg on o.shop_id = seg.warehouse_id
        where seg.deleted_at is null    
        GROUP BY year(o.created_at),
            monthname(o.created_at),
            o.shop_id, s.name,
            seg.name, seg.value

        union all

        SELECT 
            year(o.created_at) year,
            monthname(o.created_at) month,
            o.shop_id,
            s.name AS warehouse_area,
            'buying product' segment_name,
            SUM(o.grand_total) AS total_selling,
            'Conditonal' Rate,
            SUM(oi.quantity * p.purchase_price) AS total_budget

        FROM 
            orders o
        left JOIN
            order_items oi on o.id=oi.order_id
        left JOIN 
            products p ON oi.product_id = p.id
        left JOIN 
            shops s ON o.shop_id = s.id 
            
        GROUP BY year(o.created_at),
            monthname(o.created_at),
            o.shop_id, s.name

        UNION all
        SELECT 
            year(b.date) year,
            monthname(b.date) month,
            b.shop_id,
            s.name AS warehouse_area,
            'Oprasional' segment_name,
            0 AS total_selling,
            'Conditonal' Rate,
            sum(grand_total) AS total_budget

        FROM 
            budget b
        left JOIN 
            shops s ON b.shop_id = s.id 
        WHERE status=1    
            
        GROUP BY year(b.date),
            monthname(b.date),
            b.shop_id, s.name)aa 
        where warehouse_area is not null;";
        return DB::select(DB::raw($query));

    }
}
