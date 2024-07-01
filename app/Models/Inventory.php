<?php

namespace App\Models;

use Carbon\Carbon;
use App\Common\Taggable;
use App\Common\Imageable;
use App\Common\Attachable;
use App\Common\Feedbackable;
use Laravel\Scout\Searchable;
use EloquentFilter\Filterable;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Common\CascadeSoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Inspectable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Taggable, Imageable, Searchable, Filterable, Feedbackable, Attachable;

    const CONDITIONS = ['New', 'Used', 'Refurbished'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories';

    /**
     * Cascade Soft Deletes Relationships
     *
     * @var array
     */
    protected $cascadeDeletes = ['carts'];

    /**
     * The attributes that should be casted to boolean types.
     *
     * @var array
     */
    protected $casts = [
        'offer_start' => 'datetime',
        'offer_end' => 'datetime',
        'available_from' => 'datetime',
        'expiry_date' => 'datetime',
        'auction_end' => 'datetime',
        'free_shipping' => 'boolean',
        'stuff_pick' => 'boolean',
        'auctionable' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * The attributes that should be inspectable for restricted keywords.
     *
     * @var array
     */
    protected static $inspectable = [
        'title',
        'condition_note',
        'description',
        'key_features',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'shop_id',
        'title',
        'warehouse_id',
        'product_id',
        'brand',
        'supplier_id',
        'sku',
        'condition',
        'condition_note',
        'description',
        'download_limit',
        'key_features',
        'stock_quantity',
        'sold_quantity',
        'damaged_quantity',
        'user_id',
        'purchase_price',
        'sale_price',
        'offer_price',
        'offer_start',
        'offer_end',
        'shipping_weight',
        'length',
        'width',
        'height',
        'free_shipping',
        'stuff_pick',
        'available_from',
        'expiry_date',
        'min_order_quantity',
        'linked_items',
        'slug',
        'meta_title',
        'meta_description',
        'active',
        'auctionable',
        'auction_status',
        'base_price',
        'auction_end',
        'bid_accept_action',
        'expired_date',
        'uom',
    ];

    // public $asYouType = true;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $key_features = $this->key_features;

        if ($key_features && is_serialized($key_features)) {
            $key_features = implode(', ', unserialize($this->key_features));
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'description' => strip_tags($this->description),
            'stock_quantity' => $this->stock_quantity,
            'sale_price' => $this->sale_price,
            'sku' => $this->sku,
            'key_features' => $this->key_features ?? null,
            'attributes' => $this->attributeValues,
            'shop' => $this->shop->name ?? null,
            'shop_id' => $this->shop_id,
            'product' => $this->product->name,
            'product_id' => $this->product->id,
            'product_gtin' => $this->product->gtin,
            'active' => $this->active,
            'available_from' => $this->available_from,
            'expiry_date' => $this->expiry_date,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with(['product', 'shop', 'attributeValues']);
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    // public function shouldBeSearchable()
    // {
    //     return $this->isActive();
    // }

    /**
     * Get the shop of the inventory.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get all categories for the category.
     */
    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class, 'category_product','product_id', null);
    // }

    /**
     * Get the Warehouse associated with the inventory.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product of the inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    /**
     * Get the variants of the item.
     */
    public function variants()
    {
        return $this->hasMany(Inventory::class, 'parent_id')->withTrashed();
    }

    /**
     * Get the supplier for the inventory.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withDefault();
    }

    // public function manufacturer()
    // {
    //     return $this->product->belongsTo(Manufacturer::class);

    //     return $this->belongsTo(Manufacturer::class, null, null, 'manufacturer_id')
    //         ->join('products', 'manufacturers.id', '=', 'products.manufacturer_id');

    //     $instance = new Manufacturer();
    //     $instance->setTable('manufacturers');
    //     $query = $instance->newQuery();

    //     return (new BelongsTo($query, $this, 'product_id', $instance->getKeyName(), 'manufacturer'))
    //         ->join('products', 'manufacturers.id', '=', 'products.manufacturer_id')
    //         ->select(\DB::raw('manufacturers.name'));

    //     // $instance = new Manufacturer();
    //     // $instance->setTable('products');
    //     // $query = $instance->newQuery();

    //     // return (new BelongsTo($query, $this, 'product_id', $instance->getKeyName(), 'manufacturer'))
    //     //     ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
    //     //     ->select(\DB::raw('manufacturers.name'));
    // }

    /**
     * Get the packagings for the order.
     */
    public function packagings()
    {
        return $this->belongsToMany(\Incevio\Package\Packaging\Models\Packaging::class)
            ->withTimestamps();
    }

    /**
     * Get the Attributes for the inventory.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_inventory')
            ->withPivot('attribute_value_id')->withTimestamps();
    }

    /**
     * Get the attribute values that owns the SubGroup.
     */
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_inventory')
            ->withPivot('attribute_id')->withTimestamps();
    }

    /**
     * Get the carts for the product.
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
            ->withPivot('item_description', 'quantity', 'unit_price')->withTimestamps();
    }

    /**
     * Get the orders for the product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('item_description', 'quantity', 'unit_price', 'feedback_id')
            ->withTimestamps();
    }

    /**
     * Get the bids from the auction listing
     * @return HasMany
     */
    public function bids(): HasMany
    {
        return $this->hasMany(\Incevio\Package\Auction\Models\Bid::class)
            ->orderBy('amount_in_system_currency', 'desc');
    }

    /**
     * Get the user's bid for the auction listing
     * @return HasOne
     */
    public function mybid(): HasOne
    {
        return $this->hasOne(\Incevio\Package\Auction\Models\Bid::class)
            ->where('customer_id', Auth::guard('customer')->id());
    }

    /**
     * Get the top bid for the auction listing
     * @return HasOne
     */
    public function topbid(): HasOne
    {
        return $this->hasOne(\Incevio\Package\Auction\Models\Bid::class)
            ->ofMany('amount_in_system_currency', 'max');
    }

    /**
     * Get the manufacturer associated with the product.
     */
    public function getManufacturerAttribute()
    {
        return $this->product->manufacturer;
    }

    /**
     * Get action status of the inventory
     */
    public function getAuctionStatusTextAttribute()
    {
        if (!$this->auctionable) {
            return '';
        }

        if (!$this->auction_status == \Incevio\Package\Auction\Enums\AuctionStatusEnum::SUSPENDED) {
            return trans('auction::lang.auction_suspended');
        }

        if (!$this->auction_status == \Incevio\Package\Auction\Enums\AuctionStatusEnum::PAUSED) {
            return trans('auction::lang.auction_paused');
        }

        if (!$this->auction_end) {
            return trans('app.not_set');
        }

        if ($this->available_from->isFuture()) {
            return trans('app.scheduled');
        }

        if ($this->auction_end->isFuture()) {
            return trans('auction::lang.auction_running');
        }

        if ($this->auction_end->isPast()) {
            return trans('app.ended');
        }

        return trans('app.unknown');
    }

    /**
     * Get action status of the inventory
     * 
     * @return boolean
     */
    public function isAuctionRunning()
    {
        return $this->auction_end && $this->available_from->isPast() && $this->auction_end->isFuture();
    }

    /**
     * Check if the quantity is getting lower that the alert quantity
     *
     * @return boolean
     */
    public function isLowQtt()
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?: 0;

        return $this->stock_quantity <= $alert_quantity;
    }

    /**
     * Check if the is out of stock
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->stock_quantity < $this->min_order_quantity;
    }

    /**
     * Check if the item hase a valid offer price.
     * 
     * @return boolean
     */
    public function hasOffer()
    {
        if (
            ($this->offer_price > 0) &&
            ($this->offer_price < $this->sale_price) &&
            ($this->offer_start < Carbon::now()) &&
            ($this->offer_end > Carbon::now())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the model is active.
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->active != static::ACTIVE) {
            return false;
        }

        if (is_incevio_package_loaded('pharmacy')) {
            return $this->expiry_date > Carbon::now();
        }

        return true;
    }

    /**
     * Check if the item is in flash deals
     *
     * @return boolean
     */
    public function isInDeals()
    {
        $flashdeals = get_flash_deals();

        if (isset($flashdeals['listings']) && $flashdeals['listings']->find($this->id)) {
            return true;
        }

        return false;
    }

    /**
     * Return currnt sale price
     *
     * @return number
     */
    public function current_sale_price()
    {
        if ($this->auctionable) {
            return $this->base_price;
        }

        return $this->hasOffer() ? $this->offer_price : $this->sale_price;
    }

    /**
     * Return the discount percentage
     *
     * @return number
     */
    public function discount_percentage()
    {
        return $this->hasOffer() ? get_percentage_of($this->sale_price, $this->offer_price) : 0;
    }

    /**
     * Setters
     */
    public function setMinOrderQuantityAttribute($value)
    {
        $this->attributes['min_order_quantity'] = $value > 1 ? $value : 1;
    }

    public function setOfferPriceAttribute($value)
    {
        $this->attributes['offer_price'] = $value > 0 ? $value : null;
    }

    public function setWarehouseIdAttribute($value)
    {
        $this->attributes['warehouse_id'] = $value > 0 ? $value : null;
    }

    public function setSupplierIdAttribute($value)
    {
        $this->attributes['supplier_id'] = $value > 0 ? $value : null;
    }

    public function setAvailableFromAttribute($value)
    {
        if ($value) {
            $this->attributes['available_from'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }

    public function setOfferStartAttribute($value)
    {
        if ($value) {
            $this->attributes['offer_start'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }

    public function setOfferEndAttribute($value)
    {
        if ($value) {
            $this->attributes['offer_end'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }

    public function setExpiryDateAttribute($value)
    {
        if ($value) {
            $this->attributes['expiry_date'] = date('Y-m-d', strtotime($value));
        }
    }

    public function setFreeShippingAttribute($value)
    {
        $this->attributes['free_shipping'] = (bool) $value;
    }

    public function setKeyFeaturesAttribute($value)
    {
        $t_key_features = null;

        if (is_array($value)) {
            $value = array_filter($value, function ($item) {
                return !empty($item[0]);
            });
        }

        if ($value) {
            $t_key_features = is_serialized($value) ? $value : serialize($value);
        }

        $this->attributes['key_features'] = $t_key_features;
    }

    public function setLinkedItemsAttribute($value)
    {
        $this->attributes['linked_items'] = (bool) $value ? serialize($value) : null;
    }

    /**
     * Set the alert_quantity for the inventory.
     */
    // public function setAlertQuantityAttribute($value)
    // {
    //     if ($value > 1) $this->attributes['alert_quantity'] = $value;
    //     else $this->attributes['alert_quantity'] = null;
    // }

    /**
     * Getters
     */
    public function getPackagingListAttribute()
    {
        if (count($this->packagings)) {
            return $this->packagings->pluck('id')->toArray();
        }
    }

    public function getExpiryDateAttribute($value)
    {
        if ($value) {
            return date('Y-m-d', strtotime($value));
        }
    }

    // This Mutators causes the searse error
    // public function getKeyFeaturesAttribute($value)
    // {
    //     return unserialize($value);
    // }
    // public function getLinkedItemsAttribute($value)
    // {
    //     return unserialize($value);
    // }

    /**
     * Scope a query to only include available for sale .
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        // return $query;

        // return $query->where([
        //     ['active', '=', 1],
        //     ['stock_quantity', '>', 0],
        //     ['available_from', '<=', Carbon::now()]
        // ]);

        $query = $query->whereHas('shop', function ($q) {
            $q->active();
        })->where([
            ['active', '=', 1],
            // ['stock_quantity', '>', 0],
            ['available_from', '<=', Carbon::now()],
        ])->zipcode();

        // Hide out-of-stock items when enabled
        if (config('system_settings.hide_out_of_stock_items')) {
            $query = $query->where('stock_quantity', '>', 0);
        }

        // Check expiry date when pharmacy plugin is enabled
        if (is_incevio_package_loaded('pharmacy')) {
            $query = $query->where('expiry_date', '>', Carbon::now());
        }

        // Auction items
        // if (is_incevio_package_loaded('auction')) {
        //     $query = $query->where(function ($q2) {
        //         $q2->whereNull('auctionable')
        //             ->orWhere([
        //                 ['auction_status', '=', \Incevio\Package\Auction\Enums\AuctionStatusEnum::RUNNING],
        //                 ['auction_end', '>', Carbon::now()]
        //             ]);
        //     });
        // }

        return $query;
    }

    /**
     * Scope a query to only include available for sale .
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasOffer($query)
    {
        return $query->where([
            ['offer_price', '>', 0],
            ['offer_start', '<', Carbon::now()],
            ['offer_end', '>', Carbon::now()],
        ])->whereColumn('offer_price', '<', 'sale_price');
    }

    /**
     * Scope a query to only include items with free Shipping.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreeShipping($query)
    {
        return $query->where('free_shipping', 1);
    }

    /**
     * Scope a query to only include new Arraival Items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewArraivals($query)
    {
        return $query->where('inventories.created_at', '>', Carbon::now()->subDays(config('system.filter.new_arraival', 7)));
    }

    /**
     * Scope a query to only include low qtt items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowQtt($query)
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?: 0;

        return $query->where('stock_quantity', '<=', $alert_quantity);
    }

    /**
     * Scope a query to only include out of stock items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStockOut($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Scope a query to only include auction items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuction($query)
    {
        return $query->where('auctionable', 1);
    }

    /**
     * Scope a query to only include active records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $query = $query->where([
            ['active', '=', static::ACTIVE],
            ['stock_quantity', '>', 0],
            ['available_from', '<=', Carbon::now()],
        ]);

        // Check expiry date when pharmacy plugin is enabled
        if (is_incevio_package_loaded('pharmacy')) {
            $query = $query->where('expiry_date', '>', Carbon::now());
        }

        return $query;
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInActive($query)
    {
        $query = $query->where('active', '!=', static::ACTIVE)
            ->orWhere('available_from', '>', Carbon::now());

        if (is_incevio_package_loaded('pharmacy')) {
            $query = $query->orWhere(
                function ($q) {
                    $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '<', Carbon::now());
                }
            );
        }

        return $query;
    }

    /**
     * Check if the item is an hot item
     *
     * @return bool
     */
    public function isHotItem()
    {
        return $this->orders_count >= config('system.popular.hot_item.sell_count', 3);
    }

    /**
     * Returns if the item is in stock
     *
     * @return bool
     */
    public function getAvailabilityAttribute()
    {
        return $this->stock_quantity > 0 ? trans('theme.in_stock') : trans('theme.out_of_stock');
    }

    /**
     * Return the total stocks qtt including the sold items
     *
     * @return int
     */
    public function getTotalStockAttribute()
    {
        return $this->stock_quantity + $this->sold_quantity;
    }

    /**
     * Return formated title attribute
     *
     * @param $value
     * @return str
     */
    public function getTitleAttribute($value)
    {
        return str_replace("'", '', $value);
    }

    /**
     * Get the type for the item.
     *
     * @return array
     */
    public function getTypeAttribute()
    {
        return $this->product->downloadable ? trans('app.digital') : trans('app.physical');
    }

    /**
     * Returns translated name of condition
     *
     * @return string condition
     */
    public function getConditionAttribute($condition)
    {
        // Skip the mutator when ediding the model
        if (Route::currentRouteName() == 'admin.stock.inventory.edit') {
            return $condition;
        }

        switch ($condition) {
            case 'New':
                return trans('app.new');
            case 'Used':
                return trans('app.used');
            case 'Refurbished':
                return trans('app.refurbished');
        }
    }

    /**
     * Returns translated label text
     *
     * @return array labels
     */
    public function getLabels()
    {
        $labels = [];

        if ($this->isHotItem()) {
            $labels[] = trans('theme.hot_item');
        }

        if ($this->free_shipping) {
            $labels[] = trans('theme.free_shipping');
        }

        if ($this->stuff_pick) {
            $labels[] = trans('theme.stuff_pick');
        }

        if ($this->hasOffer()) {
            $labels[] = trans('theme.percent_off', ['value' => $this->discount_percentage()]);
        }

        if ($this->auctionable) {
            $labels[] = trans('auction::lang.auction');
        }

        return $labels;
    }


    /** if zipcode package active this function will return zipcode query
     * @param $query
     * @return mixed
     */
    public function scopeZipcode($query): object
    {
        if (is_incevio_package_loaded('zipcode')) {
            return $query->whereHas('shop.address', function ($builder) {
                return $builder->where('zip_code', session('zipcode_default'));
            });
        }

        return $query;
    }
}
