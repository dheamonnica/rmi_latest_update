<?php

namespace App\Repositories\Purchasing;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Inventory;
use App\Models\PurchasingItem;
use App\Models\Purchasing;
use App\Models\Shop;
use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use App\Repositories\Purchasing\PurchasingRepository;
use Carbon\Carbon;
use Google\Service\AndroidPublisher\Resource\Purchases;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EloquentPurchasing extends EloquentRepository implements BaseRepository, PurchasingRepository
{
    protected $model;

    public function __construct(Purchasing $purchasing)
    {
        $this->model = $purchasing;
    }

    public function all($status = null)
    {
        $purchasing = $this->model;

        // switch ($status) {
        //     case 'active':
        //         $purchasing = $purchasing->active();
        //         break;

        //     case 'inactive':
        //         $purchasing = $purchasing->inActive();
        //         break;

        //     case 'outOfStock':
        //         $purchasing = $purchasing->stockOut();
        //         break;
        // }

        if (!Auth::user()->isFromPlatform()) {
            return $purchasing->mine()->get();
        }

        return $purchasing->get();
    }

    // public function trashOnly()
    // {
    //     if (!Auth::user()->isFromPlatform()) {
    //         return $this->model->mine()->onlyTrashed()->with('product', 'image')->get();
    //     }

    //     return $this->model->onlyTrashed()->with('product', 'image')->get();
    // }

    public function updatePurchasingStatus(Request $request, $id) {

        $status = 1;

        $purchasing_order = $this->model->where('id', $id)->first();

        foreach($request->product as $item){
            $p_item = PurchasingItem::where(['purchasing_order_id'=> $item['purchasing_order_id'], 'product_id' => $item['product_id']])->get();

            $status = $item['shipping_status'] ?? 9;

            foreach($p_item as $purchasing_item){
                $update_p_item = PurchasingItem::find($purchasing_item->id);

                if(isset($item['shipping_status'])) {
                    switch ($item['shipping_status']) {
                        // item status
                        case Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS :
                            //2
                            $update_p_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS;
                            $update_p_item->updated_at = now();
                            $update_p_item->updated_by = Auth::user()->id;
                            # code...
                            break;
                        case Purchasing::STATUS_PURCHASING_SHIPPING_DEPATURE :
                            //3
                            $update_p_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_DEPATURE;
                            $update_p_item->depatured_at = now();
                            $update_p_item->depatured_by = Auth::user()->id;
                            $update_p_item->updated_at = now();
                            $update_p_item->updated_by = Auth::user()->id;
                            # code...
                            break;
                        case Purchasing::STATUS_PURCHASING_SHIPPING_ARRIVAL :
                            //4
                            $update_p_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_ARRIVAL;
                            $update_p_item->arrival_at = now();
                            $update_p_item->arrival_by = Auth::user()->id;
                            $update_p_item->fulfilled_at = now();
                            $update_p_item->fulfilled_by = Auth::user()->id;
                            $update_p_item->updated_at = now();
                            $update_p_item->updated_by = Auth::user()->id;
                            # code...
                            break;
                        case Purchasing::STATUS_PURCHASING_TRANSFER_SHIPMENT :
                            //5
                            $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_SHIPMENT;
                            $purchasing_order->shipped_at = now();
                            $purchasing_order->shipped_by = Auth::user()->id;
                            # code...
                            break;
                        case Purchasing::STATUS_PURCHASING_TRANSFER_STOCK :
                            //6
                            $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_STOCK;
                            $purchasing_order->transfered_stock_at = now();
                            $purchasing_order->transfered_stock_by = Auth::user()->id;
                            # code...
                            break;
                        case Purchasing::STATUS_PURCHASING_TRANSFER_COMPLETE :
                            //7
                            $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_COMPLETE;
                            $purchasing_order->transfered_complete_at = now();
                            $purchasing_order->transfered_complete_by = Auth::user()->id;
                            # code...
                            break;
                        default:
                            //first created
                            $update_p_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_CREATED;
                            $update_p_item->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_REQUESTED;
                            $update_p_item->request_status = Purchasing::STATUS_PURCHASING_REQUEST;
                            $update_p_item->updated_at = now();
                            $update_p_item->updated_by = Auth::user()->id;
                            # code...
                            break;
                    }
                }


                $update_p_item->price = $item['price'];
                $update_p_item->currency = $request->currency;
                $update_p_item->currency_amount = (int) $request->exchange_rate * (int) $item['price'];

                $update_p_item->save();
            }
        }

        
        // if($request->request_status) {
        //     $status = $request->request_status;
        //     switch ($request->request_status) {
        //         case Purchasing::STATUS_PURCHASING_DONE :
        //             //9
        //             $purchasing_order->request_status = Purchasing::STATUS_PURCHASING_DONE;
        //             $purchasing_order->done_at = now();
        //             $purchasing_order->done_by = Auth::user()->id;
        //             # code...
        //             break;
        //     }
        // }

        //update the purchasing
        switch ($status) {
            case Purchasing::STATUS_PURCHASING_SHIPPING_CREATED :
                //1
                $purchasing_order->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_CREATED;
                $purchasing_order->created_at = now();
                $purchasing_order->created_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS :
                //2
                $purchasing_order->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS;
                $purchasing_order->in_progress_at = now();
                $purchasing_order->in_progress_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_SHIPPING_DEPATURE :
                //3
                $purchasing_order->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_DEPATURE;
                $purchasing_order->depatured_at = now();
                $purchasing_order->depatured_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_SHIPPING_ARRIVAL :
                //4
                $purchasing_order->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_ARRIVAL;
                $purchasing_order->arrival_at = now();
                $purchasing_order->arrival_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_TRANSFER_SHIPMENT :
                //5
                $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_SHIPMENT;
                $purchasing_order->shipped_at = now();
                $purchasing_order->shipped_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_TRANSFER_STOCK :
                //6
                //create stock trnasfer
                
                $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_STOCK;
                $purchasing_order->transfered_stock_at = now();
                $purchasing_order->transfered_stock_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_TRANSFER_COMPLETE :
                //7
                $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_COMPLETE;
                $purchasing_order->transfered_complete_at = now();
                $purchasing_order->transfered_complete_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_REQUEST :
                //8
                $purchasing_order->request_status = Purchasing::STATUS_PURCHASING_REQUEST;
                $purchasing_order->request_at = now();
                $purchasing_order->request_by = Auth::user()->id;
                # code...
                break;
            case Purchasing::STATUS_PURCHASING_DONE :
                //9
                $purchasing_order->request_status = Purchasing::STATUS_PURCHASING_DONE;
                $purchasing_order->done_at = now();
                $purchasing_order->done_by = Auth::user()->id;

                //TODO: update inventory bogor
                # code...
                break;
            default:
                //first created
                $purchasing_order->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_CREATED;
                $purchasing_order->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_REQUESTED;
                $purchasing_order->request_status = Purchasing::STATUS_PURCHASING_REQUEST;
                $purchasing_order->updated_at = now();
                $purchasing_order->updated_by = Auth::user()->id;
                # code...
                break;
        }

        $purchasing_order->transfer_status = $request->transfer_status;
        $purchasing_order->request_status = $request->request_status;
        $purchasing_order->currency = $request->currency;
        $purchasing_order->exchange_rate = $request->exchange_rate;

        $purchasing_order->save();


        
        // $purchasing->packed_date = date("Y-m-d G:i:s"); //ikut purchasing->created_at

        // if((int) $purchasing->is_backdate) {
        //     $purchasing->packed_date = $purchasing->created_at;
        // }

        // $purchasing->purchasing_status_id = 10;
        // $purchasing->packed_by = Auth::user()->id;

        return true;

        // return true;
    }

    public function store(Request $request)
    {
        $warehouse_bogor_id = Shop::where('slug', 'warehouse-bogor')->first()->id;

        $inventory_id = null;
        $shop_id = $warehouse_bogor_id;

        foreach($request->product as $product){
            //create the stock transfer if it arrival the inventory stock is increased

            if (!Auth::user()->isFromPlatform()) {
                $item = Inventory::where(['product_id' => (int) $product['product_id'], 'shop_id' => (int) $warehouse_bogor_id])->first();   
                
                if(!$item){
                    //TODO: create if not exist. 
                    //get the product data 
                    $product = Product::find((int) $product['product_id']);
                    //product => inventory
                    /**
                     * 
                     * name => title
                     * id => product_id
                     * shop_id => $warehouse_bogor_id
                     * manufacture_skuid => sku
                     * - => condition = 'new'
                     * 0 => stock_quantity
                     * 0 => sold_quantity
                     * 0 => sale_price
                     * today => available_from
                     * 0 => length
                     * 0 => width
                     * 0 => height
                     * 0 => distance_unit
                     * 0 => expired_date
                     * 0 => uom
                     * 
                     */

                    $item = Inventory::create([
                        'title' => $product->name,
                        'product_id' => $product->id,
                        'shop_id' => $warehouse_bogor_id,
                        'sku' => $product->manufacture_skuid,
                        'slug' => $product->slug,
                        'condition' => 'new',
                        'condition_note' => 'purchasing request',
                        'stock_quantity' => 0,
                        'sold_quantity' => 0,
                        'sale_price' => 0,
                        'length' => 0,
                        'width' => 0,
                        'height' => 0,
                        'distance_unit' => 0,
                        'expired_date' => 0,
                        'user_id' => Auth::user()->id,
                        'uom' => $product->type_uom,
                    ]);
                }

                //search item in warehouse
                $shop_id = Auth::user()->merchantId();
                $inventory_id = $item->id;
            }

            $product = [
                'purchasing_order_id' => null,
                'shop_request_id' => $shop_id,
                //TODO: requester shop_id, if bogor return 22
                'inventory_id' => $inventory_id, //not null if request is warehouse //find & create inventory bogor.
                'product_id' => (int) $product['product_id'], //change to product id
                // 'manufacture_id' => $product['manufacture_skuid'], //not null
                'stock_transfer_id' => null, //not null if request is warehouse
                'request_quantity' => (int) $product['quantity'] ?? 0,
                // 'price' => (int) $product['price'] ?? 0,
                'currency' => $request->currency, //USD / CNY
                'currency_amount' => $request->currency_amount, //USD / CNY
                'currency_timestamp' => $request->currency_timestamp, //USD / CNY
                // 'timestamp_currency' => $timestamp_currency,
                // 'current_rate' => $rate,
                // 'converted_price' => $converted_rate,
                'shipment_status' => Purchasing::STATUS_PURCHASING_SHIPPING_CREATED,
                'transfer_status' => Purchasing::STATUS_PURCHASING_TRANSFER_REQUESTED,
                'request_status' => Purchasing::STATUS_PURCHASING_REQUEST,
            ];

            //stock transfer item

            PurchasingItem::create($product);
        }

        return true;
        
        //create purchasing item
    }

    public function updateManufacture($product)
    {
        foreach($product as $item){
            ProductItem::find($item->id)->update([
                'manufacture_id' => $item->manufacture_id,
            ]);
        }
    }
}
