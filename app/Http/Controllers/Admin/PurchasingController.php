<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttributeValue;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Helpers\ListHelper;
use App\Models\Manufacturer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Purchasing;
use App\Models\PurchasingItem;
use App\Models\Shop;
use App\Repositories\Purchasing\PurchasingRepository;

class PurchasingController extends Controller
{
    use Authorizable;

    private $model_name;

    private $purchasing;

    /**
     * construct
     */
    public function __construct(PurchasingRepository $purchasing)
    {
        parent::__construct();
        $this->model_name = trans('app.model.purchasing');
        $this->purchasing = $purchasing;
    }

	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchasing = $this->purchasing->all();

        $trashes = $this->purchasing->trashOnly();

        return view('admin.purchasing.index', compact('purchasing', 'trashes'));
    }

    // function will process the ajax request
    public function getPurchasing(Request $request)
    {
        // $products = Product::select('*');
        $purchasing = PurchasingItem::
        select(
            'purchasing_orders.id' ,
            'purchasing_order_items.manufacture_number' ,
            'purchasing_order_items.id as item_id' ,
            'purchasing_orders.request_at as request_date',
            'shops.name as warehouse',
            'products.name as product',
            'purchasing_order_items.request_quantity as quantity',
            'purchasing_order_items.price',
            'purchasing_order_items.shipment_status as shipment_status',
            'purchasing_orders.transfer_status as transfer_status',
            'purchasing_orders.request_status as request_status',
        )
        ->rightjoin('purchasing_orders', 'purchasing_orders.id', '=', 'purchasing_order_items.purchasing_order_id')
        ->leftjoin('shops', 'shops.id', '=', 'purchasing_orders.shop_requester_id')
        ->leftjoin('products', 'products.id', '=', 'purchasing_order_items.product_id')
        ->whereNotNull('purchasing_order_items.purchasing_order_id');

        if (!Auth::user()->isFromPlatform()) {
            $purchasing->mine()->orderBy('purchasing_orders.created_at')->get();
        } else {
            $purchasing->orderBy('purchasing_orders.created_at')->get();
        }


        // When accessing by a merchent user
        // if (Auth::user()->isFromMerchant()) {
        //     $purchasing->mine();
        // }

        return Datatables::of($purchasing)
            ->editColumn('checkbox', function ($purchasing) {
                return view('admin.partials.actions.purchasing.checkbox', compact('purchasing'));
            })
            ->addColumn('option', function ($purchasing) {
                return view('admin.partials.actions.purchasing.options', compact('purchasing'));
            })
            // ->editColumn('image', function ($purchasing) {
            //     return view('admin.partials.actions.puchasing.image', compact('purchasing'));
            // })
            ->editColumn('warehouse', function ($purchasing) {
                return $purchasing->warehouse ?? '-';
            })
            ->editColumn('price', function ($purchasing) {
                return $purchasing->price ?? '-';
            })
            ->editColumn('shipment_status', function ($purchasing) {
                return get_purchasing_status_name($purchasing->shipment_status);
            })
            ->editColumn('transfer_status', function ($purchasing) {
                return get_purchasing_status_name($purchasing->transfer_status);
            })
            ->editColumn('request_status', function ($purchasing) {
                return get_purchasing_status_name($purchasing->request_status);
            })
            // ->editColumn('quantity', function ($purchasing) {
            //     return $purchasing->type;
            // })
            // ->editColumn('gtin', function ($purchasing) {
            //     return view('admin.partials.actions.purchasing.gtin', compact('purchasing'));
            // })
            // ->editColumn('category', function ($purchasing) {
            //     return view('admin.partials.actions.purchasing.category', compact('purchasing'));
            // })
            // ->editColumn('inventories_count', function ($purchasing) {
            //     return view('admin.partials.actions.purchasing.inventories_count', compact('purchasing'));
            // })
            // ->editColumn('added_by', function ($purchasing) {
            //     return view('admin.partials.actions.purchasing.added_by', compact('purchasing'));
            // })
            ->rawColumns(['checkbox', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = Product::all();
        return view('admin.purchasing.create', compact('product'));
    }

    /**
     * Add a product to inventory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $purchasing = $this->purchasing->store($request);

        return redirect()->route('admin.purchasing.purchasing.index')->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchasing = Purchasing::find($id);


        // $this->authorize('view', $purchasing); //check permission

        return view('admin.purchasing._show', compact('purchasing'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {

        $purchasing = $this->purchasing->find($id);

        $this->authorize('view', $purchasing); // Check permission

        $purchasing->invoice('D'); // Download the invoice
    }

    /**
     * Show the fulfillment form for the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fulfillment($id)
    {
        $purchasing = $this->purchasing->find($id);

        $this->authorize('fulfill', $purchasing); // Check permission

        // $carriers = ListHelper::carriers($purchasing->shop_id);

        return view('admin.purchasing._fulfill', compact('purchasing'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Purchasing = $this->purchasing->fing($id);

        $this->authorize('edit', $purchasing);

        return view('admin.purchasing.edit', compact('order'));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $this->purchasing->restore($id);

        return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->purchasing->destroy($id);

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    public function adminNote($id)
    {
        $purchasing = $this->purchasing->find($id);

        $this->authorize('fulfill', $purchasing); // Check permission

        return view('admin.purchasing._edit_admin_note', compact('purchasing'));
    }

    public function saveAdminNote(Request $request, $id)
    {
        $purchasing = $this->purchasing->find($id);

        $this->authorize('fulfill', $purchasing); // Check permission

        $this->purchasing->updateAdminNote($request, $purchasing);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Handle the mass manufacture request and redirect to assignment view.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function massManufacture(Request $request)
    {
        // Validate request
        if (!$request->has('ids') || empty($request->ids)) {
            return back()->with('error', 'No products selected');
        }

        // Handle AJAX request
        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('admin.purchasing.purchasing.assignMassManufacture'),
                'success' => trans('messages.updated', ['model' => $this->model_name])
            ]);
        }

        // Regular request - redirect to assignment page
        return redirect()->route('admin.purchasing.purchasing.assignMassManufacture', [
            'ids' => $request->ids
        ]);
    }

    /**
     * Show the manufacture assignment view for selected products.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function assignManufacture(Request $request)
    {
        // Validate request
        if (!$request->has('ids')) {
            return back()->with('error', 'No products selected');
        }

        // Get products for assignment
        $purchasing = PurchasingItem::whereIn('id', $request->ids)->get();
        $manufacture = Manufacturer::select('id', 'name')->pluck('name', 'id');
        $warehouse = Shop::select('id', 'name')->pluck('name', 'id');
        $inv_number = get_formated_purchasing_number();
        $manufacture_number = get_formated_manufacture_number();

        if ($purchasing->isEmpty()) {
            return back()->with('error', 'No valid purchasing found');
        }

        return view('admin.purchasing._select_manufacture', [
            'inv_number' => $inv_number,
            'manufacture_number' => $manufacture_number,
            'purchasing' => $purchasing,
            'manufacture' => $manufacture,
            'warehouse' => $warehouse,
        ]);
    }

    public function generateInvoice(Request $request){
        $data = $request->all();

        $shop_id = null;
        $stock_transfer_id = null;

        if($request->shop_requester_id) {
            $shop_id = $request->shop_requester_id;
        }

        if(auth()->user()->shop_id){ //logic for warehouse
            $shop_id = auth()->user()->shop_id;

            //create stock transfer
        }

        $purchasing = Purchasing::create([
            'shop_receiver_id' => $shop_id,
            'shop_requester_id' => $shop_id,
            'stock_transfer_id' => $stock_transfer_id,
            'purchasing_invoice_number' => $request->purchasing_invoice_number,
            'purchasing_date' => date('Y-m-d'),
            'request_by' => auth()->user()->id,
            'request_at' => now(),
            'shipment_status' => (int) $request->shipment_status,
            'transfer_status' => (int) $request->transfer_status,
            'request_status' => (int) $request->request_status,
            'admin_note' => $request->admin_note,
        ]);

        if($purchasing) {
            foreach($request->product as $product){
                $item = PurchasingItem::find($product['id']);

                if(auth()->user()->shop_id) {
                    //search item in warehouse

                    $item->inventory_id = $inventory_id;
                }

                $item->manufacture_number = $request->manufacture_number;
                $item->manufacture_id = $request->manufacture;
                $item->purchasing_order_id = $purchasing->id;
                $item->stock_transfer_id = $stock_transfer_id;
                $item->price = $product['price'];
                $item->shipment_status = Purchasing::STATUS_PURCHASING_SHIPPING_IN_PROGRESS;
                $item->transfer_status = Purchasing::STATUS_PURCHASING_TRANSFER_REQUESTED;
                $item->request_status = Purchasing::STATUS_PURCHASING_REQUEST;

                $item->save();

            }
        }

        return redirect()->route('admin.purchasing.purchasing.index')->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function setShippingStatus(Request $request, $id)
    {
        //id = status_id
        // $id = $request->ids; //item_id
        // $this->authorize('fulfill', $purchasing); // Check permission

        $purchasing = $this->purchasing->updatePurchasingStatus($request, $id);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.updated', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Show stock transfer form
     *
     * @return \Illuminate\Http\Response
     */
    public function showForm()
    {
        return view('admin.inventory._stock_transfer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchWarehouse()
    {
        return view('admin.order._search_warehouse');
    }
}