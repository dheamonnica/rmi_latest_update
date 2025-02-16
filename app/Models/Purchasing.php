<?php

namespace App\Models;

// use App\Common\CascadeSoftDeletes;
use App\Common\Attachable;
use App\Common\Feedbackable;
use App\Common\Imageable;
use App\Common\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Services\PurchasingInvoice;
use Illuminate\Support\Facades\DB;
use Riskihajar\Terbilang\Facades\Terbilang;
// use Laravel\Scout\Searchable;

class Purchasing extends BaseModel
{
    use HasFactory, SoftDeletes, Taggable, Imageable, Attachable, Feedbackable;

    const STATUS_PURCHASING_SHIPPING_CREATED = 1;
    const STATUS_PURCHASING_SHIPPING_IN_PROGRESS = 2;
    const STATUS_PURCHASING_SHIPPING_DEPATURE = 3;
    const STATUS_PURCHASING_SHIPPING_ARRIVAL = 4;
    const STATUS_PURCHASING_TRANSFER_SHIPMENT = 5;
    const STATUS_PURCHASING_TRANSFER_STOCK = 6;
    const STATUS_PURCHASING_TRANSFER_COMPLETE = 7;
    const STATUS_PURCHASING_REQUEST = 8;
    const STATUS_PURCHASING_DONE = 9;
    const STATUS_PURCHASING_TRANSFER_REQUESTED = 10;


	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchasing_orders';

	 /**
     * The attributes that should be inspectable for restricted keywords.
     *
     * @var array
     */
    protected static $inspectable = [
        // 'name',
        // 'description',
    ];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_receiver_id',
        'shop_requester_id',
        'stock_transfer_id',
        'purchasing_invoice_number',
        'purchasing_date',
        'request_by',
        'request_at',
        'in_progress_by',
        'in_progress_at',
        'shipped_by',
        'shipped_at',
        'depatured_by',
        'depatured_at',
        'arrival_by',
        'arrival_at',
        'transfered_stock_by',
        'transfered_stock_at',
        'transfered_completed_by',
        'transfered_completed_at',
        'done_by',
        'done_at',
        'shipment_status',
        'transfer_status',
        'request_status',
        'currency',
        'exchange_rate',
        'currency_timestamp',
	];

    public function items() {
        return $this->hasMany(PurchasingItem::class, 'purchasing_order_id');
    }

    public function itemGroups()
    {
        return $this->hasMany(PurchasingItem::class, 'purchasing_order_id')
            ->select('product_id', 'manufacture_id','purchasing_order_id','shipment_status', 'transfer_status','shipment_status',
        'request_status', 'price',
                DB::raw('COUNT(*) as items_count'),
                DB::raw('SUM(request_quantity) as request_quantity'),
            )
            ->groupBy('product_id');
    }

    public function groupedProductIds()
    {
        return $this->hasMany(PurchasingItem::class, 'purchasing_order_id')
            ->select('id')
            ->groupBy('product_id')
            ->pluck('id');
            // ->toArray();
    }

	public function receiverWarehouse() {
		return $this->belongsTo(Shop::class, 'shop_receiver_id');
	}

	public function requesterWarehouse() {
		return $this->belongsTo(Shop::class, 'shop_requester_id');
	}

	public function requestBy()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

	public function inProgressBy()
    {
        return $this->belongsTo(User::class, 'in_progress_by');
    }

	public function shippedBy()
    {
        return $this->belongsTo(User::class, 'shipped_by');
    }

	public function depaturedBy()
    {
        return $this->belongsTo(User::class, 'depatured_by');
    }

	public function arrivalBy()
    {
        return $this->belongsTo(User::class, 'arrival_by');
    }

	public function transferedStockBy()
    {
        return $this->belongsTo(User::class, 'transfered_stock_by');
    }

	public function transferedCompletedBy()
    {
        return $this->belongsTo(User::class, 'transfered_completed_by');
    }

	public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function getWarehousePrimary($slug = 'warehouse-bogor')
    {
        return Shop::where('slug', $slug)->first();
    }

	//useful function
	 /**
     * Set tag date formate
     */
    public function setShippingDateAttribute($value)
    {
        $this->attributes['shipping_date'] = Carbon::createFromFormat('Y-m-d', $value);
    }

    public function scopeMine($query)
    {
        return $query->whereNotNull('shop_requester_id')->where('shop_requester_id', Auth::user()->merchantId());
    }

	/**
     * Scope a query to only include records from the users shop.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeArchived($query)
    {
        return $query->onlyTrashed();
    }

    /**
     * Scope a query to only include records from the users shop.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithArchived($query)
    {
        return $query->withTrashed();
    }

	/**
     * Scope a query to only include paid orders.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('shipping_status', '>=', static::STATUS_PURCHASING_SHIPPING_CREATED);
    }

	/**
     * Fulfill the purchasing
     *
     * @return $this
     */
    public function fulfill(Request $request)
    {}

	/**
     * [orderStatus description]
     *
     * @param  bool $plain [description]
     *
     * @return [type]         [description]
     */
    public function shippingStatus($plain = false)
    {}

	/**
     * [orderStatus description]
     *
     * @param  bool $plain [description]
     *
     * @return [type]         [description]
     */
    public function transferStatus($plain = false)
    {}

	/**
     * [orderStatus description]
     *
     * @param  bool $plain [description]
     *
     * @return [type]         [description]
     */
    public function requestStatus($plain = false)
    {}

	 /**
     * Render PDF invoice
     * 
     * @param  string $des I => Display on browser, D => Force Download, F => local path save, S => return document as string
     * @param string $folder_path file path where generated files will be temporarily saved.
     */
    public function invoice($des = 'D',$file_path = null)
    {
        $title = (bool) config('invoice.title') ?
        config('invoice.title') :
        trans('invoice.purchasing_invoice');

        $invoice = new PurchasingInvoice();
        $invoice->setDocumentOrientation('L');
        $invoice->setColor(config('invoice.color', '#007fff'));      // pdf color scheme
        $invoice->setDocumentSize('A4');      // set document size
        $invoice->setType($title);    // Invoice Type

        // $invoice->setLogo(get_logo_url('system', 'logo'), 100, 80);
        $invoice->setLogo('https://rmi-testing.ideaprojects.my.id/image/images/logo.png', 75, 75);

        $manufacture_number = '-';

        if($this->items->count() > 0){
            $manufactures = $this->items[0]->manufacture;
            $manufacture_number = $this->items[0]->manufacture_number;
            $invoice->setManufactureNumber($manufacture_number);
            // $manufactureAddress = $this->item[0]->manufacture->address->primaryAddress ?? $this->item[0]->manufacture->address;
            // Replace the address type with manufacture shop name
            $manufacture_name = $manufactures->name;
            $manufacture_email = $manufactures->email;
            $manufacture_country = $manufactures->country->name;
            $manufacture = [$manufacture_name,'', $manufacture_email, $manufacture_country];
            // Reset the array keys
            // $manufacture = array_values($manufacture);
            // $manufacture = addressToArray(formatIndexedArrayAddress($manufacture));

            $invoice->setFrom($manufacture);
        }

        if($this->shop_requester_id){
            $vendorAddress = $this->requesterWarehouse->primaryAddress ?? $this->requesterWarehouse->address;
            $invoiceFrom = $vendorAddress ? $vendorAddress->toArray() : [];
            // Replace the address type with vendor shop name
            $warehouse_dest = $this->requesterWarehouse->legal_name ?? $this->requesterWarehouse->name;
            // Reset the array keys
            $invoiceFrom = array_values($invoiceFrom);
            $invoiceFrom = addressToArray(formatIndexedArrayAddress($invoiceFrom));
            array_unshift($invoiceFrom, $warehouse_dest);

            // $invoice->setTo([$warehouse_dest,'Alamat Bogor']);
            $invoice->setTo($invoiceFrom);
        } else {
            $vendorAddress = $this->getWarehousePrimary()->primaryAddress ?? $this->getWarehousePrimary()->address;
            $invoiceFrom = $vendorAddress ? $vendorAddress->toArray() : [];
            // Replace the address type with vendor shop name
            $warehouse_dest = $this->getWarehousePrimary()->legal_name ?? $this->getWarehousePrimary()->name;
            // Reset the array keys
            $invoiceFrom = array_values($invoiceFrom);
            $invoiceFrom = addressToArray(formatIndexedArrayAddress($invoiceFrom));
            array_unshift($invoiceFrom, $warehouse_dest);

            // $invoice->setTo([$warehouse_dest,'Alamat Bogor']);
            $invoice->setTo($invoiceFrom);


        }
                

        // $invoice->setReceiverName('Warehouse Bogor');

        $invoice->setPurchasingInvoiceNumber($this->purchasing_invoice_number);

        $invoice->setDate($this->created_at->format('M d, Y'));
        $invoice->setTime($this->created_at->format('h:i:s A'));

        $total = 0;
        $total_price = 0;

        if($this->itemGroups->count() > 0){
            foreach ($this->itemGroups as $item) {
                $invoice->addItem($item->product->name, '',$item->product->manufacture_skuid, $item->product->selling_skuid, $item->request_quantity, $item->price, $item->img);

                $total += ($item->request_quantity * $item->price);
            }
        }

        $total_converted =  get_formated_decimal($total / $this->exchange_rate, true, 2);

        $invoice->addSummary(trans('invoice.total'), $total);
        $invoice->addSummary(trans('invoice.currency'), $this->currency);
        $invoice->addSummary(trans('invoice.currency_time'), $this->currency_timestamp);
        $invoice->addSummary(trans('invoice.rate'), $this->exchange_rate);
        $invoice->addSummary(trans('invoice.total_currency'), $this->currency." ".$total_converted);

        $invoice->setNetAmount($total);
        $invoice->setNetAmountCurrency($this->currency." ".$total_converted);
        $invoice->setNetAmountWord(Terbilang::make($total));
        /**
         * setCurrency -> CNY or USD
         * manufacture name setFromName
         * manufacture address setFrom
         * 
         * send to (default) 
         * wh name setReceiverName
         * address setTo
         * 
         * items list addItem
         * 
         * amount setNetAmount
         * say setNetAmountWord
         * 
         * qty total
         * tax
         * grand total
         */

        $invoice->setFooternote(get_platform_title() . ' | ' . url('/') . ' | ' . trans('invoice.footer_note'));

        if($des =='F'){
            $invoice->render($file_path, $des);
        } else{
            $invoice->render(get_platform_title() . '-' . $this->purchasing_invoice_number . '.pdf', $des);
        }

    }

	public static function getPurchasingReport()
    {}
}