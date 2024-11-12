<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Events\Order\OrderCreated;
use App\Events\Order\OrderFulfilled;
use App\Events\Order\OrderUpdated;
use App\Helpers\ListHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateOrderRequest;
use App\Http\Requests\Validations\FulfillOrderRequest;
use App\Http\Requests\Validations\DeliveredConfirmedOrderRequest;
use App\Models\Order;
use App\Models\Merchant;
use App\Models\Customer;
use App\Repositories\Order\OrderRepository;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\URL;
use ZipArchive;
use Carbon\Carbon;

// use App\Services\PdfInvoice;
// use Konekt\PdfInvoice\InvoicePrinter;

class OrderController extends Controller
{
    use Authorizable;

    private $model_name;

    private $order;

    /**
     * construct
     */
    public function __construct(OrderRepository $order)
    {
        parent::__construct();
        $this->model_name = trans('app.model.order');
        $this->order = $order;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fulfilment = Route::is('admin.order.pickup') ? Order::FULFILMENT_TYPE_PICKUP : Order::FULFILMENT_TYPE_DELIVER;

        $orders = $this->order->all($fulfilment);

        $archives = $this->order->trashOnly();

        $deliveryBoysUser = ListHelper::deliveryBoyRole();

        return view('admin.order.index', compact('orders', 'archives', 'deliveryBoysUser'));
    }

    public function exportIndex()
    {
        $fulfilment = Route::is('admin.order.pickup') ? Order::FULFILMENT_TYPE_PICKUP : Order::FULFILMENT_TYPE_DELIVER;

        $orders = $this->order->all($fulfilment);

        $archives = $this->order->trashOnly();

        $deliveryBoysUser = ListHelper::deliveryBoyRole();

        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        $customers = Customer::whereNotNull('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.order.export_index', compact('orders', 'archives', 'deliveryBoysUser', 'merchants', 'customers'));
    }

    /**
     * Display a page to process bulk order processing.
     * @param Request
     * @param int For filtering by payment status
     * @param int For filtering by order status
     * @return \Illuminate\Http\Response
     */
    public function showBulkProcess(Request $request, $paymentStatus = 0, $orderStatus = 0)
    {
        $fulfilment = Route::is('admin.order.pickup') ? Order::FULFILMENT_TYPE_PICKUP : Order::FULFILMENT_TYPE_DELIVER;

        $orders = Order::where('fulfilment_type', $fulfilment);

        if (Auth::user()->role_id === 9) {
            $orders = Order::where('delivery_boy_id', Auth::user()->id);
        }

        if (Auth::user()->isFromMerchant()) {
            $orders->where('shop_id', Auth::user()->merchantId()); // Merchants must only see their own orders
        }

        if ($paymentStatus != 0) {
            ($paymentStatus == Order::PAYMENT_STATUS_PAID) ? $orders->paid() : $orders->unpaid();
        }

        if ($orderStatus != 0) {
            $orders->where('order_status_id', $orderStatus);
        }

        $orders = $orders->orderBy('created_at', 'desc')->get();

        return Datatables::of($orders)
            ->editColumn('checkbox', function ($order) {
                return view('admin.partials.actions.order.checkbox', compact('order'));
            })
            ->addColumn('order', function ($order) {
                return view('admin.partials.actions.order.order', compact('order'));
            })
            ->addColumn('created_by', function ($order) {
                return view('admin.partials.actions.order.order_created_by', compact('order'));
            })
            ->addColumn('po_number_ref', function ($order) {
                return view('admin.partials.actions.order.order_po_number_ref', compact('order'));
            })
            ->addColumn('packed_by', function ($order) {
                return view('admin.partials.actions.order.order_packed_by', compact('order'));
            })
            ->addColumn('packed_date', function ($order) {
                return view('admin.partials.actions.order.order_packed_date', compact('order'));
            })
            ->addColumn('shipped_by', function ($order) {
                return view('admin.partials.actions.order.order_shipped_by', compact('order'));
            })
            ->addColumn('shipping_date', function ($order) {
                return view('admin.partials.actions.order.order_shipped_date', compact('order'));
            })
            ->addColumn('delivery_by', function ($order) {
                return view('admin.partials.actions.order.order_delivery_by', compact('order'));
            })
            ->addColumn('delivery_date', function ($order) {
                return view('admin.partials.actions.order.order_delivery_date', compact('order'));
            })
            ->addColumn('due_date_payment', function ($order) {
                return view('admin.partials.actions.order.order_due_date_payment', compact('order'));
            })
            ->addColumn('due_days_payment', function ($order) {
                return view('admin.partials.actions.order.order_due_days_payment', compact('order'));
            })
            ->addColumn('cancel_date', function ($order) {
                return view('admin.partials.actions.order.order_cancel_date', compact('order'));
            })
            ->addColumn('cancel_by', function ($order) {
                return view('admin.partials.actions.order.order_cancel_by', compact('order'));
            })
            ->addColumn('paid_by', function ($order) {
                return view('admin.partials.actions.order.order_paid_by', compact('order'));
            })
            ->addColumn('paid_date', function ($order) {
                return view('admin.partials.actions.order.order_paid_date', compact('order'));
            })
            ->addColumn('order_date', function ($order) {
                return view('admin.partials.actions.order.order_date', compact('order'));
            })
            ->editColumn('delivery_boy', function ($order) {
                return view('admin.partials.actions.order.delivery_boy', compact('order'));
            })
            ->editColumn('shop', function ($order) {
                return view('admin.partials.actions.order.shop', compact('order'));
            })
            ->editColumn('customer_name', function ($order) {
                return view('admin.partials.actions.order.customer_name', compact('order'));
            })
            ->editColumn('product_qty', function ($order) {
                return view('admin.partials.actions.order.order_product_qty', compact('order'));
            })
            ->editColumn('grand_total', function ($order) {
                return view('admin.partials.actions.order.grand_total', compact('order'));
            })
            ->editColumn('grand_total_number', function ($order) {
                return view('admin.partials.actions.order.grand_total_number', compact('order'));
            })
            ->editColumn('grand_total', function ($order) {
                return get_formated_currency($order->grand_total, 2);
            })
            ->editColumn('payment_status', function ($order) {
                return view('admin.partials.actions.order.payment_status', compact('order'));
            })
            ->editColumn('partial_status', function ($order) {
                return view('admin.partials.actions.order.order_partial', compact('order'));
            })
            ->editColumn('order_status', function ($order) {
                $order_statuses = \App\Helpers\ListHelper::order_statuses();
                return view('admin.partials.actions.order.order_status', compact('order', 'order_statuses'));
            })
            ->editColumn('option', function ($order) {
                return view('admin.partials.actions.order.option', compact('order'));
            })
            ->rawColumns(['checkbox', 'order', 'po_number_ref', 'order_date', 'created_by', 'packed_date', 'shipped_by', 'shipping_date', 'delivery_by', 'delivery_date', 'due_date_payment', 'due_days_payment', 'cancel_by', 'cancel_date', 'paid_by', 'paid_date', 'shop', 'customer_name', 'order_product_qty', 'grand_total', 'payment_status', 'partial_status', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchCustomer()
    {
        return view('admin.order._search_customer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data['customer'] = $this->order->getCustomer($request->input('customer_id'));

        $data['cart_lists'] = $this->order->getCartList($request->input('customer_id'));

        if ($request->input('cart_id')) {
            $data['cart'] = $this->order->getCart($request->input('cart_id'));
        }

        return view('admin.order.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrderRequest $request)
    {
        if (is_null($request->input('cart'))) {
            return back()->with('warning', trans('theme.notify.cart_empty'));
        }

        $order = $this->order->store($request);

        event(new OrderCreated($order));

        return redirect()->route('admin.order.order.index')
            ->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = $this->order->find($id);

        $this->authorize('view', $order); // Check permission

        $address = $order->customer->primaryAddress();

        $deliveryBoysUser = ListHelper::deliveryBoyRole();

        return view('admin.order.show', compact('order', 'address', 'deliveryBoysUser'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $order = $this->order->find($id);

        $this->authorize('view', $order); // Check permission

        $order->invoice('D'); // Download the invoice
    }

    /**
     * Download invoices of all selected orders
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function downloadSelected(Request $request)
    {
        $filePaths = array();
        $folder_name = $this->getUniqueFolderNameForInvoice();

        foreach ($request->ids as $id) {
            $order = Order::find($id);
            $this->authorize('view', $order); // Check permission

            $file_name = get_platform_title() . '_' . $order->order_number . '.pdf';
            $file_path = public_path('invoice_tmp/' . $folder_name . '/' . $file_name);
            $folder_path = public_path('invoice_tmp/' . $folder_name);

            if (!file_exists($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $order->invoice('F', $file_path); // Generate PDF

            // Store generated file paths for zipping and deletion
            array_push($filePaths, $file_path);
        }

        // Create ZIP archive
        $zip = new ZipArchive();
        $zipFileName = 'Invoices.zip';
        $zipFilePath = public_path('invoice_tmp/' . $folder_name . '/' . $zipFileName);

        // If a file at zipFilePath exists delete the existing file
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE)) {
            foreach ($filePaths as $filePath) {
                $relativeName = basename($filePath);
                $zip->addFile($filePath, $relativeName);
            }
        }

        $zip->close();

        // Delete the files used to create the zip file
        foreach ($filePaths as $filePath) {
            \File::delete($filePath);
        }

        $zipFilePath = URL::to('/' . 'invoice_tmp/' . $folder_name . '/' . $zipFileName);

        $response = [
            'download' => trans('messages.created', ['model' => $this->model_name]),
            'download_url' => URL::to($zipFilePath),
            'download_file_name' => 'Invoices.zip',
        ];

        // Prepare response data
        if ($request->ajax()) {
            return response()->json($response);
        }

        return response()->json(['error' => trans('messages.failed')]);
    }

    /**
     * Show the fulfillment form for the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fulfillment($id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $carriers = ListHelper::carriers($order->shop_id);

        return view('admin.order._fulfill', compact('order', 'carriers'));
    }

    public function deliveredConfirmation($id)
    {
        $order = $this->order->find($id);

        return view('admin.order._delivered_fulfill', compact('order'));
    }

    public function deliveryBoys($id)
    {
        $order = $this->order->find($id);

        $deliveryboys = ListHelper::deliveryBoys($order->shop_id);

        $deliveryBoysUser = ListHelper::deliveryBoyRole();

        return view('admin.order._assign_delivery_boy', compact('deliveryboys', 'deliveryBoysUser', 'order'));
    }

    public function assignDeliveryBoy(Request $request, $id)
    {
        $order = $this->order->find($id);

        $order->delivery_boy_id = $request->delivery_boy_id;
        $order->save();

        $deliveryBoy_token = optional($order->deliveryBoy)->fcm_token;

        if (!is_null($deliveryBoy_token)) {
            FCMService::send($deliveryBoy_token, [
                'title' => trans('notifications.order_assigned.subject', ['order' => $order->order_number]),
                'body' => trans('notifications.order_assigned.message'),
            ]);
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        return view('admin.order._edit', compact('order'));
    }

    /**
     * Fulfill the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function fulfill(FulfillOrderRequest $request, $id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->fulfill($request, $order);

        event(new OrderFulfilled($order, $request->filled('notify_customer')));

        if (config('shop_settings.auto_archive_order') && $order->isPaid()) {
            $this->order->trash($id);

            return redirect()->route('admin.order.order.index')
                ->with('success', trans('messages.fulfilled_and_archived'));
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Fulfill the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function deliveredConfirmed(DeliveredConfirmedOrderRequest $request, $id)
    {
        $order = $this->order->find($id);
        $this->authorize('fulfill', $order); // Check permission

        $this->order->confimedDelivered($request, $order);

        $this->order->updateStatusDelivered($request, $order);

        // event(new OrderFulfilled($order, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Update Order Status of the selected orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $status
     * @return \Illuminate\Http\Response
     */
    public function massAssignOrderStatus(Request $request)
    {
        $orders = Order::whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            $this->authorize('fulfill', $order);

            $order->order_status_id = $request->status;
            $order->save();

            event(new OrderUpdated($order, $request->filled('notify_customer')));
        }

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.updated', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * updateOrderStatus the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->updateOrderStatus($request, $order);

        event(new OrderUpdated($order, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function saveDueDatePayment(Request $request, $id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->updateDueDatePayment($request, $order);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function adminNote($id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        return view('admin.order._edit_admin_note', compact('order'));
    }

    public function saveAdminNote(Request $request, $id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->updateAdminNote($request, $order);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function setAsDelivered(Request $request, $id)
    {

        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->updateStatusDelivered($request, $order);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function setAsPacked(Request $request, $id)
    {

        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->updateStatusPacked($request, $order);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, $id)
    {
        $this->order->trash($id);

        return redirect()->route('admin.order.order.index')
            ->with('success', trans('messages.archived', ['model' => $this->model_name]));
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
        $this->order->restore($id);

        return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
    }

    /**
     * Assign Payment Status of the given orders, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request Request contains ids of checked/selected orders
     * @param  string|null  $assign  The payment status to assign (paid, unpaid, refunded)
     * @return \Illuminate\Http\Response
     */
    public function massAssignPaymentStatus(Request $request, $assign = null)
    {
        $orders = Order::whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            $this->authorize('fulfill', $order);

            switch ($assign) {
                case 'paid':
                    $order->markAsPaid();
                    break;
                case 'unpaid':
                    $order->markAsUnpaid();
                    break;
                case 'refunded':
                    $order->markAsRefunded();
                    break;
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.updated', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Toggle Payment Status of the given order, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function togglePaymentStatus(Request $request, $id)
    {
        if (Auth::user()->isFromMerchant() && !vendor_get_paid_directly()) {
            return back()->with('warning', trans('messages.failed', ['model' => $this->model_name]));
        }

        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        if ($order->isPaid()) {
            $order->markAsUnpaid();
        } else {
            $order->markAsPaid();
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
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
        $this->order->destroy($id);

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Empty the Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emptyTrash(Request $request)
    {
        $this->order->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Get the unique folder name for invoice
     *
     * @return string
     */
    private function getUniqueFolderNameForInvoice()
    {
        return Auth::user()->isFromMerchant() ? 'merchant' . Auth::user()->merchantId() . 'shop' . Auth::user()->shop->id : 'admin';
    }

    public function getOrderReport(Request $request)
    {
        $orders = Order::getOrderReport();
        return Datatables::of($orders)
            ->addColumn('order_number', function ($order) {
                return $order->order_number;
            })
            ->addColumn('po_number_ref', function ($order) {
                return $order->po_number_ref;
            })
            ->addColumn('warehouse_name', function ($order) {
                return $order->warehouse_name;
            })
            ->addColumn('client_name', function ($order) {
                return $order->client_name;
            })
            ->addColumn('selling_skuid', function ($order) {
                return $order->selling_skuid;
            })
            ->addColumn('product_name', function ($order) {
                return $order->product_name;
            })
            ->addColumn('quantity', function ($order) {
                return $order->quantity;
            })
            ->addColumn('unit_price', function ($order) {
                return round($order->unit_price);
            })
            ->addColumn('purchase_price', function ($order) {
                return $order->purchase_price;
            })
            ->addColumn('total', function ($order) {
                return round($order->total);
            })
            ->addColumn('discount', function ($order) {
                return $order->discount;
            })
            ->addColumn('taxrate', function ($order) {
                return $order->taxrate;
            })
            ->addColumn('Grand_Total', function ($order) {
                return $order->Grand_Total;
            })

            ->addColumn('created_at', function ($order) {
                return $order->created_at;
            })
            ->addColumn('created_by', function ($order) {
                return $order->created_by;
            })
            ->addColumn('packed_date', function ($order) {
                return $order->packed_date;
            })
            ->addColumn('packed_by', function ($order) {
                return $order->packed_by;
            })
            ->addColumn('shipping_date', function ($order) {
                return $order->shipping_date;
            })
            ->addColumn('shipped_by', function ($order) {
                return $order->shipped_by;
            })
            ->addColumn('delivery_date', function ($order) {
                return $order->delivery_date;
            })
            ->addColumn('delivered_by', function ($order) {
                return $order->delivered_by;
            })
            ->addColumn('paid_date', function ($order) {
                return $order->paid_date;
            })
            ->addColumn('paid_by', function ($order) {
                return $order->paid_by;
            })

            ->addColumn('SLA_Order', function ($order) {
                if ($order->SLA_Order == 0) {
                    return "";
                }

                $class = $order->SLA_Order >= 15 ? 'label label-danger' : 'label label-info';
                return "<span class='$class'>{$order->SLA_Order}</span>";
            })
            ->addColumn('SLA_Packing', function ($order) {
                if ($order->SLA_Packing == 0) {
                    return "";
                }

                $class = $order->SLA_Packing >= 15 ? 'label label-danger' : 'label label-info';
                return "<span class='$class'>{$order->SLA_Packing}</span>";
            })
            ->addColumn('SLA_Delivery', function ($order) {
                if ($order->SLA_Delivery == 0) {
                    return "";
                }

                $class = $order->SLA_Delivery >= 90 ? 'label label-danger' : 'label label-info';
                return "<span class='$class'>{$order->SLA_Delivery}</span>";
            })
            ->addColumn('SLA_Payment', function ($order) {
                if ($order->SLA_Payment == 0) {
                    return "";
                }

                $class = $order->SLA_Payment >= 40 ? 'label label-danger' : 'label label-info';
                return "<span class='$class'>{$order->SLA_Payment}</span>";
            })

            ->addColumn('due_date_in_days', function ($order) {
                return $order->due_date_in_days;
            })
            ->addColumn('due_date', function ($order) {
                return $order->due_date;
            })
            ->addColumn('cancel_date', function ($order) {
                return $order->cancel_date;
            })
            ->addColumn('cancel_by', function ($order) {
                return $order->cancel_by;
            })
            ->addColumn('payment_status', function ($order) {
                // payment status:
                // 1. unpaid
                // 2. pending
                // 3. paid
                // 4. refund initiated
                // 5. partially refunded
                // 6. refunded
                if ($order->payment_status == 1) {
                    return '<span class="label label-danger">Awaiting payment</span>';
                } else if ($order->payment_status == 2) {
                    return '<span class="label label-default">Pending</span>';
                } else if ($order->payment_status == 3) {
                    return '<span class="label label-info">Paid</span>';
                } else if ($order->payment_status == 4) {
                    return '<span class="label label-default">Refund Initiated</span>';
                } else if ($order->payment_status == 5) {
                    return '<span class="label label-default">Partially Refunde</span>';
                } else if ($order->payment_status == 6) {
                    return '<span class="label label-default">Refunded</span>';
                }
            })
            ->addColumn('order_status_id', function ($order) {
                // order status:
                // 1. waiting for payment
                // 2. payment error
                // 3. confirmed
                // 4. fullfiled
                // 5. awaiting delivery
                // 6. delivered
                // 7. refunded
                // 8. cancelled
                // 9. disputed
                // 10. packed
                if ($order->order_status_id == 1) {
                    return '<span class="label label-default">Waiting for payment</span>';
                } else if ($order->order_status_id == 2) {
                    return '<span class="label label-default">Payment error</span>';
                } else if ($order->order_status_id == 3) {
                    return '<span class="label label-default">Confirmed</span>';
                } else if ($order->order_status_id == 4) {
                    return '<span class="label label-info">Fullfilled</span>';
                } else if ($order->order_status_id == 5) {
                    return '<span class="label label-default">Awaiting delivery</span>';
                } else if ($order->order_status_id == 6) {
                    return '<span class="label label-success">Delivered</span>';
                } else if ($order->order_status_id == 7) {
                    return '<span class="label label-default">Refunded</span>';
                } else if ($order->order_status_id == 8) {
                    return '<span class="label label-default">Cancelled</span>';
                } else if ($order->order_status_id == 9) {
                    return '<span class="label label-default">Disputed</span>';
                } else if ($order->order_status_id == 10) {
                    return '<span class="label label-default">Waiting for Packed</span>';
                }
            })

            ->rawColumns(['order_number', 'po_number_ref', 'warehouse_name', 'client_name', 'selling_skuid', 'product_name', 'quantity', 'unit_price', 'purchase_price', 'total', 'discount', 'taxrate', 'Grand_Total', 'created_at', 'created_by', 'packed_date', 'packed_by', 'shipping_date', 'shipped_by', 'delivery_date', 'delivered_by', 'paid_date', 'paid_by', 'SLA_Order', 'SLA_Packing', 'SLA_Delivery', 'SLA_Payment', 'due_date_in_days', 'due_date', 'cancel_date', 'cancel_by', 'payment_status', 'order_status_id'])
            ->make(true);
    }

    public function paymentDocument()
    {
        $fulfilment = Route::is('admin.order.pickup') ? Order::FULFILMENT_TYPE_PICKUP : Order::FULFILMENT_TYPE_DELIVER;

        $orders = $this->order->all($fulfilment);

        $archives = $this->order->trashOnly();

        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        $customers = Customer::whereNotNull('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.order.payment_document', compact('orders', 'archives', 'merchants', 'customers'));
    }

    public function getOrderPaymentDocReport(Request $request)
    {
        $fulfilment = Route::is('admin.order.pickup') ? Order::FULFILMENT_TYPE_PICKUP : Order::FULFILMENT_TYPE_DELIVER;

        $orders = $this->order->all($fulfilment);
        return Datatables::of($orders)
            ->addColumn('created_at', function ($order) {
                return $order->created_at;
            })
            ->addColumn('order_number', function ($order) {
                return $order->order_number;
            })
            ->addColumn('po_number_ref', function ($order) {
                return $order->po_number_ref;
            })
            ->addColumn('shop_id', function ($order) {
                return $order->getWarehouse->name;
            })
            ->addColumn('customer_id', function ($order) {
                return $order->getCustomer->name;
            })
            ->addColumn('doc_SI', function ($order) {
                $url = route('admin.order.order.invoice', $order->id);
                return "<a href='{$url}'>
                            <i data-toggle='tooltip' data-placement='top' title='Download Invoice' class='fa fa-download'></i>
                        </a>";
            })
            ->addColumn('paid_date', function ($order) {
                return $order->paid_date;
            })
            ->addColumn('doc_faktur_pajak', function ($order) {
                if (empty($order->doc_faktur_pajak)) {
                    "";
                } else {
                    return "<a href='" . asset('storage/' . $order->doc_faktur_pajak) . "' target='_blank'>Dokumen Faktur Pajak</a>";
                }
            })
            ->addColumn('doc_faktur_pajak_uploaded_at', function ($order) {
                return $order->doc_faktur_pajak_uploaded_at;
            })
            ->addColumn('sla_faktur_pajak', function ($order) {
                $start = Carbon::parse($order->paid_date);
                $end = Carbon::parse($order->doc_faktur_pajak_uploaded_at);

                // Get the difference in minutes
                $diffInMinutes = $start->diffInMinutes($end);

                $class = $diffInMinutes >= 15 ? 'label label-danger' : 'label label-info';
                return "<span class='$class'>{$diffInMinutes}</span>";
            })
            ->addColumn('doc_faktur_pajak_terbayar', function ($order) {
                if (empty($order->doc_faktur_pajak_terbayar)) {
                    "";
                } else {
                    return "<a href='" . asset('storage/' . $order->doc_faktur_pajak_terbayar) . "' target='_blank'>Dokumen Tukar Faktur Pajak Terbayar</a>";
                }
            })
            ->addColumn('doc_faktur_pajak_terbayar_uploaded_at', function ($order) {
                return $order->doc_faktur_pajak_terbayar_uploaded_at;
            })
            ->addColumn('sla_faktur_pajak_terbayar', function ($order) {
                $start = Carbon::parse($order->doc_faktur_pajak_uploaded_at);
                $end = Carbon::parse($order->doc_faktur_pajak_terbayar_uploaded_at);

                // Get the difference in days
                $diffInDays = $start->diffInDays($end);

                $class = $diffInDays >= 3 ? 'label label-danger' : 'label label-info';
                return "<span class='$class'>{$diffInDays}</span>";
            })
            ->addColumn('payment_status', function ($order) {
                // payment status:
                // 1. unpaid
                // 2. pending
                // 3. paid
                // 4. refund initiated
                // 5. partially refunded
                // 6. refunded
                if ($order->payment_status == 1) {
                    return '<span class="label label-danger">Awaiting payment</span>';
                } else if ($order->payment_status == 2) {
                    return '<span class="label label-default">Pending</span>';
                } else if ($order->payment_status == 3) {
                    return '<span class="label label-info">Paid</span>';
                } else if ($order->payment_status == 4) {
                    return '<span class="label label-default">Refund Initiated</span>';
                } else if ($order->payment_status == 5) {
                    return '<span class="label label-default">Partially Refunde</span>';
                } else if ($order->payment_status == 6) {
                    return '<span class="label label-default">Refunded</span>';
                }
            })
            ->addColumn('order_status_id', function ($order) {
                // order status:
                // 1. waiting for payment
                // 2. payment error
                // 3. confirmed
                // 4. fullfiled
                // 5. awaiting delivery
                // 6. delivered
                // 7. refunded
                // 8. cancelled
                // 9. disputed
                // 10. packed
                if ($order->order_status_id == 1) {
                    return '<span class="label label-default">Waiting for payment</span>';
                } else if ($order->order_status_id == 2) {
                    return '<span class="label label-default">Payment error</span>';
                } else if ($order->order_status_id == 3) {
                    return '<span class="label label-default">Confirmed</span>';
                } else if ($order->order_status_id == 4) {
                    return '<span class="label label-info">Fullfilled</span>';
                } else if ($order->order_status_id == 5) {
                    return '<span class="label label-default">Awaiting delivery</span>';
                } else if ($order->order_status_id == 6) {
                    return '<span class="label label-success">Delivered</span>';
                } else if ($order->order_status_id == 7) {
                    return '<span class="label label-default">Refunded</span>';
                } else if ($order->order_status_id == 8) {
                    return '<span class="label label-default">Cancelled</span>';
                } else if ($order->order_status_id == 9) {
                    return '<span class="label label-default">Disputed</span>';
                } else if ($order->order_status_id == 10) {
                    return '<span class="label label-default">Waiting for Packed</span>';
                }
            })
            ->editColumn('options', function ($order) {
                return view('admin.partials.actions.order.option_payment', compact('order'));
            })

            ->rawColumns(['created_at', 'order_number', 'po_number_ref', 'shop_id', 'customer_id', 'doc_SI', 'doc_si_uploaded_at', 'doc_faktur_pajak', 'doc_faktur_pajak_uploaded_at', 'sla_faktur_pajak', 'doc_faktur_pajak_terbayar', 'doc_faktur_pajak_terbayar_uploaded_at', 'sla_faktur_pajak_terbayar', 'payment_status', 'order_status_id', 'options'])
            ->make(true);
    }

    public function orderPaymentEdit($id)
    {
        $order = $this->order->find($id);

        return view('admin.order.payment._edit', compact('order'));
    }

    public function updateOrderPayment(Request $request, $id)
    {
        $orderId = $request->input('id');
        $orderData = Order::find($orderId);

        if ($request->file('doc_faktur_pajak') && $request->file('doc_faktur_pajak_terbayar')) {
            // DOC FAKTUR PAJAK
            $pdfFileFP = $request->file('doc_faktur_pajak');
            $originalFilenameFP = now()->format('d-m-Y') . '/PoNumberRef_' . str_replace('/', '_', $orderData->po_number_ref) . '/' . 'FP_' . $pdfFileFP->getClientOriginalName(); // Add a timestamp to the original filename
            $pdfFileFP->storeAs('payment_documents', $originalFilenameFP, 'public');
            $orderData->doc_faktur_pajak = 'payment_documents/' . $originalFilenameFP;
            $orderData->doc_faktur_pajak_uploaded_at = $request->input('doc_faktur_pajak_uploaded_at');

            // DOC FAKTUR PAJAK TERBAYAR
            $pdfFileFPT = $request->file('doc_faktur_pajak_terbayar');
            $originalFilenameFPT = now()->format('d-m-Y') . '/PoNumberRef_' . str_replace('/', '_', $orderData->po_number_ref) . '/' . 'FPT_' . $pdfFileFPT->getClientOriginalName(); // Add a timestamp to the original filename
            $pdfFileFPT->storeAs('payment_documents', $originalFilenameFPT, 'public');
            $orderData->doc_faktur_pajak_terbayar = 'payment_documents/' . $originalFilenameFPT;
            $orderData->doc_faktur_pajak_terbayar_uploaded_at = $request->input('doc_faktur_pajak_terbayar_uploaded_at');

            $orderData->save();
        } else if ($request->file('doc_faktur_pajak')) {
            // DOC FAKTUR PAJAK
            $pdfFileFP = $request->file('doc_faktur_pajak');
            $originalFilenameFP = now()->format('d-m-Y') . '/PoNumberRef_' . str_replace('/', '_', $orderData->po_number_ref) . '/' . 'FP_' . $pdfFileFP->getClientOriginalName(); // Add a timestamp to the original filename
            $pdfFileFP->storeAs('payment_documents', $originalFilenameFP, 'public');
            $orderData->doc_faktur_pajak = 'payment_documents/' . $originalFilenameFP;
            $orderData->doc_faktur_pajak_uploaded_at = $request->input('doc_faktur_pajak_uploaded_at');

            $orderData->save();
        } else if ($request->file('doc_faktur_pajak_terbayar')) {
            // DOC FAKTUR PAJAK TERBAYAR
            $pdfFileFPT = $request->file('doc_faktur_pajak_terbayar');
            $originalFilenameFPT = now()->format('d-m-Y') . '/PoNumberRef_' . str_replace('/', '_', $orderData->po_number_ref) . '/' . 'FPT_' . $pdfFileFPT->getClientOriginalName(); // Add a timestamp to the original filename
            $pdfFileFPT->storeAs('payment_documents', $originalFilenameFPT, 'public');
            $orderData->doc_faktur_pajak_terbayar = 'payment_documents/' . $originalFilenameFPT;
            $orderData->doc_faktur_pajak_terbayar_uploaded_at = $request->input('doc_faktur_pajak_terbayar_uploaded_at');

            $orderData->save();
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }
}
