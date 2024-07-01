<?php

namespace App\Models;

use App\Common\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends BaseModel
{
    use HasFactory, SoftDeletes, Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'manufacturers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'email',
        'url',
        'phone',
        'description',
        'country_id',
        'active',
        'manufacture_pic_name',
        'manufacture_pic_email',
        'manufacture_pic_phone'
    ];

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the country for the manufacturer.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the products for the manufacturer.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all of the inventories for the country.
     */
    public function inventories()
    {
        return $this->hasManyThrough(Inventory::class, Product::class);
    }

    /**
     * Get the count of all inventories for this brand.
     */
    public function inventoryCount()
    {
        return $this->hasManyThrough(Inventory::class, Product::class)->count();
    }
}
