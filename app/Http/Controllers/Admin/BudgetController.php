<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateBudgetRequest;
use App\Http\Requests\Validations\UpdateBudgetRequest;
use App\Models\Requirement;
use App\Repositories\Budget\BudgetRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Merchant;
use App\Models\Budget;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;

class BudgetController extends Controller
{
    use Authorizable;

    private $model_name;

    private $budget;

    /**
     * construct
     */
    public function __construct(BudgetRepository $budget)
    {
        parent::__construct();

        $this->model_name = trans('app.model.budget');

        $this->budget = $budget;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        $years = Budget::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $budgets = $this->budget->all();

        $trashes = $this->budget->trashOnly();

        return view('admin.budget.index', compact('merchants', 'years', 'budgets', 'trashes'));
    }

    public function report()
    {
        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        $years = Budget::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $budgets = $this->budget->all();

        $trashes = $this->budget->trashOnly();

        return view('admin.budget.report', compact('merchants', 'years', 'budgets', 'trashes'));
    }

    public function getBudgets(Request $request)
    {
        $budgets = $this->budget->all();

        return Datatables::of($budgets)
            ->addColumn('checkbox', function ($budget) {
                return view('admin.budget.partials.checkbox', compact('budget'));
            })
            ->addColumn('date', function ($budget) {
                return view('admin.budget.partials.date', compact('budget'));
            })
            ->addColumn('month', function ($budget) {
                return view('admin.budget.partials.month', compact('budget'));
            })
            ->addColumn('year', function ($budget) {
                return view('admin.budget.partials.year', compact('budget'));
            })
            ->addColumn('requirement', function ($budget) {
                return view('admin.budget.partials.requirement', compact('budget'));
            })
            ->addColumn('category', function ($budget) {
                return view('admin.budget.partials.category', compact('budget'));
            })
            ->addColumn('qty', function ($budget) {
                return view('admin.budget.partials.qty', compact('budget'));
            })
            ->addColumn('total', function ($budget) {
                return view('admin.budget.partials.total', compact('budget'));
            })
            ->addColumn('grand_total', function ($budget) {
                return view('admin.budget.partials.grand_total', compact('budget'));
            })
            ->addColumn('warehouse', function ($budget) {
                return view('admin.budget.partials.warehouse', compact('budget'));
            })
            ->addColumn('picture', function ($budget) {
                return view('admin.budget.partials.picture', compact('budget'));
            })
            ->addColumn('status', function ($budget) {
                return view('admin.budget.partials.status', compact('budget'));
            })
            ->addColumn('created_by', function ($budget) {
                return view('admin.budget.partials.created_by', compact('budget'));
            })
            ->addColumn('created_at', function ($budget) {
                return view('admin.budget.partials.created_at', compact('budget'));
            })
            ->addColumn('updated_at', function ($budget) {
                return view('admin.budget.partials.updated_at', compact('budget'));
            })
            ->addColumn('updated_by', function ($budget) {
                return view('admin.budget.partials.updated_by', compact('budget'));
            })
            ->addColumn('approved_at', function ($budget) {
                return view('admin.budget.partials.approved_at', compact('budget'));
            })
            ->addColumn('approved_by', function ($budget) {
                return view('admin.budget.partials.approved_by', compact('budget'));
            })
            ->addColumn('option', function ($budget) {
                return view('admin.budget.partials.options', compact('budget'));
            })

            ->rawColumns(['checkbox', 'date', 'year', 'month', 'requirement', 'category', 'qty', 'total', 'grand_total', 'warehouse', 'picture', 'status', 'created_by', 'created_at', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'option'])
            ->make(true);
    }

    public function reportAdministrator()
    {
        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        $years = Budget::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $trashes = $this->budget->trashOnly();

        return view('admin.budget.report-administrator', compact('merchants', 'years', 'trashes'));
    }

    public function getBudgetsTablesReportAdministrator(Request $request)
    {
        $budgets = Budget::getReportDataHeaderAdministrator();

        return Datatables::of($budgets)
            ->addColumn('month', function ($budget) {
                return $budget->month;
            })
            ->addColumn('year', function ($budget) {
                return $budget->year;
            })
            ->addColumn('total_budget', function ($budget) {
                return 'Rp. ' . number_format($budget->total_budget, 0, '.', '.');
            })
            ->addColumn('total_selling', function ($budget) {
                return 'Rp. ' . number_format($budget->total_selling, 0, '.', '.');
            })
            ->addColumn('rate_cost', function ($budget) {
                return number_format($budget->rate_cost, 2, '.', '.') . '%';
            })
            ->rawColumns(['month', 'year', 'total_budget', 'total_selling', 'rate_cost'])
            ->make(true);

        return response()->json(['data' => $results]);
    }

    public function getBudgetTablesExpandAdministrator(Request $request)
    {
        $results = Budget::getReportDataExpandAdministrator();

        return response()->json(['data' => $results]);
    }

    public function getBudgetsTablesExpandClientAdministrator(Request $request)
    {
        $results = Budget::getReportDataExpandClientAdministrator();

        return response()->json(['data' => $results]);
    }

    public function getBudgetCategoryValue(Request $request)
    {
        $id = $request->query('id');
        $results = Budget::getBudgetCategoryValue($id);

        return response()->json(['data' => $results]);
    }

    public function getBudgetData(Request $request)
    {
        $id = $request->query('id');
        $results = Budget::getBudgetData($id);

        return response()->json(['data' => $results]);
    }

    public function getBudgetsReport(Request $request)
    {
        $budgets = Inventory::select('inventories.shop_id', 'shops.name as shop_name')
            ->selectRaw('SUM(inventories.stock_quantity * products.purchase_price) as buying_product')
            ->selectRaw('SUM(orders.grand_total) as total_selling')
            ->join('shops', 'inventories.shop_id', '=', 'shops.id')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->leftJoin('orders', 'inventories.shop_id', '=', 'orders.shop_id')
            ->when(Auth::user()->role_id === 8 || Auth::user()->role_id === 13, function ($query) {
                return $query->where('inventories.shop_id', Auth::user()->shop_id); // assuming the shop_id is available on the user
            })
            ->groupBy('inventories.shop_id', 'shops.name')
            ->get();

        return Datatables::of($budgets)
            ->addColumn('month', function ($budget) {
                return view('admin.budget.partials.month', compact('budget'));
            })
            ->addColumn('year', function ($budget) {
                return view('admin.budget.partials.year', compact('budget'));
            })
            ->addColumn('business_unit', function ($budget) {
                return $budget->shop_name;
            })
            ->addColumn('buying_product', function ($budget) {
                return 'Rp. ' . number_format($budget->buying_product, 2, '.', '.');
            })
            ->addColumn('fee_management', function ($budget) {
                return 'Rp. ' . number_format(($budget->total_selling * 7 / 100), 2, '.', '.');
            })
            ->addColumn('marketing', function ($budget) {
                return 'Rp. ' . number_format(($budget->total_selling * 5 / 100), 2, '.', '.');
            })
            ->addColumn('operational', function ($budget) {
                return 'Rp. ' . number_format(($budget->total_selling * 20 / 100), 2, '.', '.');
            })
            ->addColumn('total_budget', function ($budget) {
                $buying_product = $budget->buying_product;
                $fee_management = $budget->total_selling * 7 / 100;
                $marketing = $budget->total_selling * 5 / 100;
                $operational = $budget->total_selling * 20 / 100;
                $total_budget = $buying_product + $fee_management + $marketing + $operational;

                return 'Rp. ' . number_format($total_budget, 2, '.', '.');
            })
            ->addColumn('total_selling', function ($budget) {
                return 'Rp. ' . number_format($budget->total_selling, 2, '.', '.');
            })
            ->addColumn('achieve', function ($budget) {
                $achieve = ($budget->total_selling / $budget->buying_product) * 100;
                return number_format($achieve, 2, '.', '.') . '%';
            })
            ->addColumn('status', function ($budget) {
                $achieve = ($budget->total_selling / $budget->buying_product) * 100;
                $status = $achieve >= 100 ? '<span class="label label-primary">ACHIEVE</span>' : '<span class="label label-danger">FAIL</span>';
                return $status;
            })

            ->rawColumns(['month', 'year', 'business_unit', 'buying_product', 'fee_management', 'marketing', 'operational', 'total_budget', 'achieve', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $budgetCategories = Requirement::whereNull('deleted_at')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
        return view('admin.budget._create', compact('budgetCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBudgetRequest $request)
    {
        // $request['grand_total'] = $request->input('total') * $request->input('qty');
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->budget->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     return redirect()->route('admin.offering.index');
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $budget = $this->budget->find($id);
        $budgetCategories = Requirement::whereNull('deleted_at')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.budget._edit', compact('budget', 'budgetCategories'));
    }

    public function setApprove(Request $request, $id)
    {

        $budget = $this->budget->find($id);

        $this->budget->updateStatusApprove($request, $budget);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBudgetRequest $request, $id)
    {
        // $request['grand_total'] = $request->input('total') * $request->input('qty');
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->budget->update($request, $id);

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
        $this->budget->trash($id);

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
        $this->budget->restore($id);

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
        $this->budget->destroy($id);

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
        $this->budget->massTrash($request->ids);

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
        $this->budget->massDestroy($request->ids);

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
        $this->budget->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}