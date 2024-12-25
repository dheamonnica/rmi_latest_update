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
	];

    public function items() {
        return $this->hasMany(PurchasingItem::class, 'purchasing_order_id');
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

        $invoice->setLogo(get_logo_url('system', 'logo'), 100, 80);

        $manufacture_number = '-';

        if($this->items->count() > 0){
            $manufacture_number = $this->items[0]->manufacture_number;
            $invoice->setManufactureNumber($manufacture_number);
        }

        $invoice->setPurchasingInvoiceNumber($this->purchasing_invoice_number);

        $invoice->setDate($this->created_at->format('M d, Y'));
        $invoice->setTime($this->created_at->format('h:i:s A'));

        if($this->items->count() > 0){
            foreach ($this->items as $item) {
                $invoice->addItem($item->product->name, '',$item->manufacture->name, $item->request_quantity, $item->price);
            }
        }
        
        
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