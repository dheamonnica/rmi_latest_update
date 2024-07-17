<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCRMRequest;
use App\Http\Requests\Validations\UpdateCRMRequest;
use App\Repositories\CRM\CRMRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\CRM;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;

class CRMController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $crm;

    /**
     * construct
     */
    public function __construct(CRMRepository $crm)
    {
        parent::__construct();

        $this->model_name = trans('app.model.crm');

        $this->crm = $crm;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $crms = $this->crm->all();

        $trashes = $this->crm->trashOnly();

        $merchants = Merchant::get()->pluck('warehouse_name', 'id')->toArray();

        $years = CRM::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.crm.index', compact('trashes', 'crms', 'merchants', 'years'));
    }

    public function getCRMsTables(Request $request)
    {
        $crms = $this->crm->all();

        return Datatables::of($crms)
            ->addColumn('checkbox', function ($crm) {
                return view('admin.crm.partials.checkbox', compact('crm'));
            })
            ->addColumn('month', function ($crm) {
                return view('admin.crm.partials.month', compact('crm'));
            })
            ->addColumn('year', function ($crm) {
                return view('admin.crm.partials.year', compact('crm'));
            })
            ->addColumn('warehouse', function ($crm) {
                return view('admin.crm.partials.warehouse', compact('crm'));
            })
            ->addColumn('client', function ($crm) {
                return view('admin.crm.partials.client', compact('crm'));
            })
            ->addColumn('picture', function ($crm) {
                return view('admin.crm.partials.picture', compact('crm'));
            })
            ->addColumn('total_plan', function ($crm) {
                if (Auth::user()->role_id === 1 || Auth::user()->role_id === 13) {
                    $getTotalPlanVisit = Visit::selectRaw('COUNT(id) as total_plan')
                        ->where('status', 0)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalPlanVisit = Visit::selectRaw('COUNT(id) as total_plan')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->where('status', 0)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }
                return view('admin.crm.partials.total_plan', compact('crm', 'getTotalPlanVisit'));
            })
            ->addColumn('total_plan_actual', function ($crm) {
                if (Auth::user()->role_id === 1 || Auth::user()->role_id === 13) {
                    $getTotalVerifiedVisit = Visit::selectRaw('COUNT(id) as total_plan_actual')
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalVerifiedVisit = Visit::selectRaw('COUNT(id) as total_plan_actual')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }
                return view('admin.crm.partials.total_plan_actual', compact('crm', 'getTotalVerifiedVisit'));
            })
            ->addColumn('success_rate', function ($crm) {

                if (Auth::user()->role_id === 1 || Auth::user()->role_id === 13) {
                    $getTotalPlanVisit = Visit::selectRaw('COUNT(id) as total_plan')
                        ->where('status', 0)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalPlanVisit = Visit::selectRaw('COUNT(id) as total_plan')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->where('status', 0)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }

                if (Auth::user()->role_id === 1 || Auth::user()->role_id === 13) {
                    $getTotalVerifiedVisit = Visit::selectRaw('COUNT(id) as total_plan_actual')
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalVerifiedVisit = Visit::selectRaw('COUNT(id) as total_plan_actual')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }
                return view('admin.crm.partials.success_rate', compact('crm', 'getTotalPlanVisit', 'getTotalVerifiedVisit'));
            })

            ->addColumn('status', function ($crm) {

                if (Auth::user()->role_id === 1 || Auth::user()->role_id === 13) {
                    $getTotalPlanVisit = Visit::selectRaw('COUNT(id) as total_plan')
                        ->where('status', 0)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalPlanVisit = Visit::selectRaw('COUNT(id) as total_plan')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->where('status', 0)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }

                if (Auth::user()->role_id === 1 || Auth::user()->role_id === 13) {
                    $getTotalVerifiedVisit = Visit::selectRaw('COUNT(id) as total_plan_actual')
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                } else {
                    $getTotalVerifiedVisit = Visit::selectRaw('COUNT(id) as total_plan_actual')
                        ->where('shop_id', Auth::user()->shop_id)
                        ->where('status', 1)
                        ->whereNull('deleted_at')
                        ->orWhere('deleted_at', '')
                        ->first();
                }
                return view('admin.crm.partials.status', compact('crm', 'getTotalPlanVisit', 'getTotalVerifiedVisit'));
            })
            ->addColumn('options', function ($crm) {
                return view('admin.crm.partials.options', compact('crm'));
            })

            ->rawColumns(['checkbox', 'month', 'year', 'warehouse', 'client', 'picture', 'total_plan', 'total_plan_actual', 'success_rate', 'status', 'options'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::get()->pluck('name', 'id')->toArray();

        return view('admin.crm._create', compact('customers'));
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    public function store(CreateCRMRequest $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->crm->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function setApprove(Request $request, $id)
    {

        $crm = $this->crm->find($id);

        $this->crm->updateStatusApprove($request, $crm);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $crm = $this->crm->find($id);
        $users = User::get()->pluck('full_name', 'id')->toArray();
        $customers = Customer::get()->pluck('name', 'id')->toArray();

        return view('admin.crm._edit', compact('crm', 'users', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCRMRequest $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->crm->update($request, $id);

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
        $this->crm->trash($id);

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
        $this->crm->restore($id);

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
        $this->crm->destroy($id);

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
        $this->crm->massTrash($request->ids);

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
        $this->crm->massDestroy($request->ids);

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
        $this->crm->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}