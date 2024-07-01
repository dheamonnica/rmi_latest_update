<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateBudgetRequest;
use App\Http\Requests\Validations\UpdateBudgetRequest;
use App\Repositories\Budget\BudgetRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Merchant;
use App\Models\Budget;
use App\Models\Order;

// use App\Models\Inventory;

class BudgetController extends Controller
{
    // use Authorizable;

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
        $merchants = Merchant::get()->pluck('warehouse_name', 'id')->toArray();

        $years = Budget::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $budgets = $this->budget->all();

        $trashes = $this->budget->trashOnly();

        $getTotalIncomebyShop = Order::selectRaw('SUM(grand_total) as total_grand_total')
            ->where('shop_id', 19)
            ->whereNull('deleted_at')
            ->orWhere('deleted_at', '')
            ->first();

        return view('admin.budget.index', compact('merchants', 'years', 'budgets', 'trashes', 'getTotalIncomebyShop'));
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
            ->addColumn('option', function ($budget) {
                return view('admin.budget.partials.options', compact('budget'));
            })

            ->rawColumns(['checkbox', 'date', 'month', 'year', 'requirement', 'qty', 'total', 'grand_total', 'picture', 'created_by', 'created_at', 'updated_by', 'updated_by', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.budget._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBudgetRequest $request)
    {
        $request['grand_total'] = $request->input('total') * $request->input('qty');
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
        return view('admin.budget._edit', compact('budget'));
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
        $request['grand_total'] = $request->input('total') * $request->input('qty');
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