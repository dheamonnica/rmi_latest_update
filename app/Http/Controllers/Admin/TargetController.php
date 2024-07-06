<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateTargetRequest;
use App\Http\Requests\Validations\UpdateTargetRequest;
use App\Repositories\Target\TargetRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Merchant;
use App\Models\Target;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $target;

    /**
     * construct
     */
    public function __construct(TargetRepository $target)
    {
        parent::__construct();

        $this->model_name = trans('app.model.target');

        $this->target = $target;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchants = Merchant::get()->pluck('warehouse_name', 'id')->toArray();

        $years = Target::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $targets = $this->target->all();

        $trashes = $this->target->trashOnly();

        if (Auth::user()->isAdmin() || Auth::user()->isMerchant() || Auth::user()->isFromPlatform()) {
            $getTotalIncomebyShop = Order::selectRaw('SUM(grand_total) as total_grand_total')
                ->where('shop_id', Auth::user()->shop_id)
                ->whereNull('deleted_at')
                ->orWhere('deleted_at', '')
                ->first();
        } else {
            $getTotalIncomebyShop = Order::selectRaw('SUM(grand_total) as total_grand_total')
                ->whereNull('deleted_at')
                ->orWhere('deleted_at', '')
                ->first();
        }

        return view('admin.target.index', compact('merchants', 'years', 'targets', 'trashes', 'getTotalIncomebyShop'));
    }

    public function getTargetsTables(Request $request)
    {
        $targets = $this->target->all();

        return Datatables::of($targets)
            ->addColumn('checkbox', function ($target) {
                return view('admin.target.partials.checkbox', compact('target'));
            })
            ->addColumn('date', function ($target) {
                return view('admin.target.partials.date', compact('target'));
            })
            ->addColumn('month', function ($target) {
                return view('admin.target.partials.month', compact('target'));
            })
            ->addColumn('year', function ($target) {
                return view('admin.target.partials.year', compact('target'));
            })
            ->addColumn('hospital_name', function ($target) {
                return view('admin.target.partials.hospital_name', compact('target'));
            })
            ->addColumn('grand_total', function ($target) {
                return view('admin.target.partials.grand_total', compact('target'));
            })
            ->addColumn('actual_sales', function ($getTotalIncomebyShop) {
                if (Auth::user()->isAdmin() || Auth::user()->isMerchant() || Auth::user()->isFromPlatform()) {
                    $getTotalIncomebyShop = Order::selectRaw('SUM(grand_total) as total_grand_total')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalIncomebyShop = Order::selectRaw('SUM(grand_total) as total_grand_total')
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }

                return view('admin.target.partials.actual_sales', compact('getTotalIncomebyShop'));
            })
            ->addColumn('warehouse', function ($target) {
                return view('admin.target.partials.warehouse', compact('target'));
            })
            ->addColumn('created_by', function ($target) {
                return view('admin.target.partials.created_by', compact('target'));
            })
            ->addColumn('created_at', function ($target) {
                return view('admin.target.partials.created_at', compact('target'));
            })
            ->addColumn('updated_at', function ($target) {
                return view('admin.target.partials.updated_at', compact('target'));
            })
            ->addColumn('updated_by', function ($target) {
                return view('admin.target.partials.updated_by', compact('target'));
            })
            ->addColumn('option', function ($target) {
                return view('admin.target.partials.options', compact('target'));
            })

            ->rawColumns(['checkbox', 'date', 'month', 'year', 'hospital_name', 'grand_total', 'actual_sales', 'warehouse', 'created_by', 'created_at', 'updated_at', 'updated_by', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hospital_name = Customer::get()->pluck('name', 'name')->toArray();

        return view('admin.target._create', compact('hospital_name'));
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    public function store(CreatetargetRequest $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->target->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $target = $this->target->find($id);
        $hospital_name = Customer::get()->pluck('name', 'name')->toArray();

        return view('admin.target._edit', compact('target', 'hospital_name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatetargetRequest $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->target->update($request, $id);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, $id)
    {
        $this->target->trash($id);

        return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
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
        $this->target->restore($id);

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
        $this->target->destroy($id);

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massTrash(Request $request)
    {
        $this->target->massTrash($request->ids);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.trashed', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        $this->target->massDestroy($request->ids);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

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
        $this->target->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}