<?php

namespace App\Repositories\Order;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Common\ImageUploadManual;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class EloquentOrder extends EloquentRepository implements BaseRepository, OrderRepository
{

    use ImageUploadManual;

    protected $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function all($fulfilment = null)
    {
        if (!$fulfilment) {
            $fulfilment = Order::FULFILMENT_TYPE_DELIVER;
        }

        $query = $this->model->with('customer', 'shop:id,name', 'dispute:id,order_id', 'deliveryBoy')
            ->where('fulfilment_type', $fulfilment)
            ->orderBy('created_at', 'desc');

        if (Auth::user()->isFromPlatform()) {
            return $query->get();
        }

        return $query->mine()->get();
    }

    public function latest()
    {
        $query = $this->model->with('customer')->latest()->limit(10);

        if (Auth::user()->isFromPlatform()) {
            return $query->get();
        }

        return $query->mine()->get();
    }

    public function trashOnly()
    {
        $query = $this->model->archived()
            ->where('fulfilment_type', Order::FULFILMENT_TYPE_DELIVER)
            ->orderBy('deleted_at', 'desc');

        if (Auth::user()->isFromPlatform()) {
            return $query->get();
        }

        return $query->mine()->get();
    }

    public function getCart($id)
    {
        return Cart::find($id);
    }

    public function getCustomer($id)
    {
        return Customer::findOrFail($id);
    }

    public function getCartList($customerId)
    {
        return Cart::mine()->where('customer_id', $customerId)
            ->where('deleted_at', null)->with('inventories', 'customer')
            ->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        setAdditionalCartInfo($request); // Set some system information using helper function

        if ($request->input('backdate')) {

            $backdate = Carbon::createFromFormat('Y-m-d h:i a', $request->input('backdate'))
                ->format('Ymd');
            
            $timestamp = Carbon::createFromFormat('Y-m-d h:i a', $request->input('backdate'))
                ->setTimezone('UTC');
            
            $request['created_at'] = $timestamp->format('Y-m-d H:i:s');
            $request['updated_at'] = $timestamp->format('Y-m-d H:i:s');
            $request['is_backdate']= 1;
            $request['order_number'] = preg_replace('/\d{8}/', $backdate, $request->order_number);
        }
        
        $order = parent::store($request);

        $this->syncInventory($order, $request->input('cart'));

        // DELETE THE SAVED CART AFTER THE ORDER
        if ($request->input('delete_the_cart')) {
            Cart::find($request->input('cart_id'))->forceDelete();
        }

        return $order;
    }

    public function find($order)
    {
        return $this->model->withTrashed()->find($order);
    }

    public function fulfill(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        if ($request->hasFile('images')) {
           //add path /images/po_number_ref/abcd.png
           $file = $this->saveImage($request->images, str_replace('/','-',$order->po_number_ref));

            $order->confirmed_shipping_image = $file['path'];
        }

        $order->shipping_date = date('Y-m-d');

        if ($request->input('backdate')) {
            $order->shipping_date = Carbon::createFromFormat('Y-m-d', $request->input('backdate'))->format('Y-m-d');
        }

        $order->shipped_by = Auth::user()->id;
        $order->update($request->all());

        if ($order->hasPendingCancellationRequest()) {
            $order->cancellation->decline();
        }

        return $order;
    }

    public function confimedDelivered(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        if ($request->hasFile('images')) {
            //add path /images/po_number_ref/abcd.png
            $file = $this->saveImage($request->images, str_replace('/','-',$order->po_number_ref));

            $order->confirmed_delivered_image = $file['path'];
        }

        if ($request->signed) {
            //add path /images/po_number_ref/abcd.png
            $signed = $this->saveDigitalSignImage($request->signed, str_replace('/','-',$order->po_number_ref));

            $order->hash_sign = $request->signed;
            $order->digital_sign_image = $signed;
        }

        //update receiver name 
        $order->receiver_name = $request->receiver_name;

        return $order;
    }

    public function updateOrderStatus(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        $order->order_status_id = $request->input('order_status_id');

        return $order->save();
    }


    public function updateDueDatePayment(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        $order->due_date_payment = $request->input('payment_terms');

        return $order->save();
    }

    public function updateAdminNote(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        $order->admin_note = $request->input('admin_note');

        return $order->save();
    }

    public function updateStatusDelivered(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        $order->delivery_date = date("Y-m-d");

        if ($request->input('backdate')) {
            $order->delivery_date = Carbon::createFromFormat('Y-m-d', $request->input('backdate'))->format('Y-m-d');
        }

        $order->order_status_id = 6;
        $order->delivery_by = Auth::user()->id;

        return $order->save();
    }

    public function updateStatusPacked(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = $this->model->find($order);
        }

        $order->packed_date = date("Y-m-d G:i:s"); //ikut order->created_at

        if((int) $order->is_backdate) {
            $order->packed_date = $order->created_at;
        }

        $order->order_status_id = 10;
        $order->packed_by = Auth::user()->id;;

        return $order->save();
    }

    /**
     * Sync up the inventory
     * @param  Order $order
     * @param  array $items
     * @return void
     */
    public function syncInventory($order, array $items)
    {
        // Increase stock if any item removed from the order
        if ($order->inventories->count() > 0) {
            $newItems = array_column($items, 'inventory_id');

            foreach ($order->inventories as $inventory) {
                if (!in_array($inventory->id, $newItems)) {
                    Inventory::find($inventory->id)->increment('stock_quantity', $inventory->pivot->quantity);
                }
            }
        }

        $temp = [];

        foreach ($items as $item) {
            Log::info('Cart Item:', ['item' => $item]);
            $item = (object) $item;
            $id = $item->inventory_id;

            // Preparing data for the pivot table
            $temp[$id] = [
                'item_description' => $item->item_description,
                'quantity' => $item->quantity,
                'request_quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'product_id' => $item->product_id,
                'created_at' => $order->created_at, 
                'is_backdate' => $order->is_backdate, 
                //is_backdate true
            ];

            // adjust stock qtt based on tth order
            if ($order->inventories->contains($id)) {
                $old = $order->inventories()->where('inventory_id', $id)->first();
                $old_qtt = $old->pivot->quantity;
                
                 /**
                  * Qty (dari Case di atas ini hanya bisa di input 5 Sesuai Stock) tapi Jika Order 10 Di stocknya kosong kita ttp bisa bikin Po meskipun stocknya kurang - Di stock inventory akan minus 
                  * simulasi
                  * $old_qtt = 5
                  * $item->quantity = 10
                  * if (5 > 10) //false
                  * else if (5 < 10 ) //true
                  *  -> 10 - 5 = 5 => change into 5 - 10 = -5 (differential)
                  *  -> update order_items -> is_partial = true
                  *  -> update orders -> partial_status_id = true
                  */

                if ($old_qtt > $item->quantity) {
                    Inventory::find($id)->increment('stock_quantity', $old_qtt - $item->quantity);
                } elseif ($old_qtt < $item->quantity) {
                    // Inventory::find($id)->decrement('stock_quantity', $item->quantity - $old_qtt);
                    Inventory::find($id)->decrement('stock_quantity', $item->quantity - $old_qtt);

                    $order->update('partial_status_id', 1);
                }
            } else {
                Inventory::find($id)->decrement('stock_quantity', $item->quantity);
            }
        }

        // Sync the pivot table
        if (!empty($temp)) {
            $order->inventories()->sync($temp);
        }
    }

    /**
     * remove permanently
     */
    public function destroy($id)
    {
        $model = $this->model->onlyTrashed()->findOrFail($id);

        return $model->forceDelete();
    }
}
