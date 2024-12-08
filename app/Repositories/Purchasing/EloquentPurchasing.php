<?php

namespace App\Repositories\Purchasing;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Purchasing;
use App\Models\PurchasingItem;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use App\Repositories\Purchasing\PurchasingRepository;
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

    public function updatePurchasingStatus(Request $request, $status) {

        if(!$status) {
            $status = $request->transfer_status;
        }
        // if (!$purchasing instanceof Purchasing) {
        //     $purchasing = $this->model->find($purchasing);
        // }

        //update status for items. 
        foreach($request->ids as $id){
            $purchasing_item = PurchasingItem::find($id);

            $purchasing_id = $purchasing_item->purchasing_order_id;

            switch ($status) {
                // item status
                case Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS :
                    //2
                    $purchasing_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS;
                    $purchasing_item->updated_at = now();
                    $purchasing_item->updated_by = Auth::user()->id;
                    # code...
                    break;
                case Purchasing::STATUS_PURCHASING_SHIPPING_DEPATURE :
                    //3
                    $purchasing_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_DEPATURE;
                    $purchasing_item->depatured_at = now();
                    $purchasing_item->depatured_by = Auth::user()->id;
                    $purchasing_item->updated_at = now();
                    $purchasing_item->updated_by = Auth::user()->id;
                    # code...
                    break;
                case Purchasing::STATUS_PURCHASING_SHIPPING_ARRIVAL :
                    //4
                    $purchasing_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_ARRIVAL;
                    $purchasing_item->arrival_at = now();
                    $purchasing_item->arrival_by = Auth::user()->id;
                    $purchasing_item->fulfilled_at = now();
                    $purchasing_item->fulfilled_by = Auth::user()->id;
                    $purchasing_item->updated_at = now();
                    $purchasing_item->updated_by = Auth::user()->id;
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
                    $purchasing_item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_CREATED;
                    $purchasing_item->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_REQUESTED;
                    $purchasing_item->request_status = Purchasing::STATUS_PURCHASING_REQUEST;
                    $purchasing_item->updated_at = now();
                    $purchasing_item->updated_by = Auth::user()->id;
                    # code...
                    break;
            }

            if($request->request_status) {
                switch ($request->request_status) {
                    case Purchasing::STATUS_PURCHASING_DONE :
                        //9
                        $purchasing_order->request_status = Purchasing::STATUS_PURCHASING_DONE;
                        $purchasing_order->done_at = now();
                        $purchasing_order->done_by = Auth::user()->id;
                        # code...
                        break;
                }
            }

            $purchasing_item->save();

            $purchasing_order = $this->model->where('id', $purchasing_id)->first();

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
                    $purchasing_order->request = now();
                    $purchasing_order->request_by = Auth::user()->id;
                    # code...
                    break;
                case Purchasing::STATUS_PURCHASING_DONE :
                    //9
                    $purchasing_order->request_status = Purchasing::STATUS_PURCHASING_DONE;
                    $purchasing_order->done_at = now();
                    $purchasing_order->done_by = Auth::user()->id;
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

            $purchasing_order->save();
        } 


        
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
            foreach($request->product as $product){
                $product = [
                    'purchasing_order_id' => null,
                    'inventory_id' => null, //not null if request is warehouse
                    'product_id' => (int) $product['product_id'], //change to product id
                    // 'manufacture_id' => $product['manufacture_skuid'], //not null
                    'stock_transfer_id' => null, //not null if request is warehouse
                    'request_quantity' => (int) $product['quantity'] ?? 0,
                    // 'price' => (int) $product['price'] ?? 0,
                    'shipment_status' => Purchasing::STATUS_PURCHASING_SHIPPING_CREATED,
                    'transfer_status' => Purchasing::STATUS_PURCHASING_TRANSFER_REQUESTED,
                    'request_status' => Purchasing::STATUS_PURCHASING_REQUEST,
                ];

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
