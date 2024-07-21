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
use App\Models\Shop;
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

    public function data()
    {
        $crms = $this->crm->all();

        $trashes = $this->crm->trashOnly();

        $merchants = Merchant::get()->pluck('warehouse_name', 'id')->toArray();

        $years = CRM::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.crm.data', compact('trashes', 'crms', 'merchants', 'years'));
    }

    public function getCRMsTables(Request $request)
    {
        $query = Visit::selectRaw('
            *,
            shop_id,
            YEAR(date) as year,
            MONTH(date) as month,
            COUNT(id) as total_plan,
            COUNT(CASE WHEN status = 1 THEN 1 END) as total_plan_actual
        ')
            ->whereNull('deleted_at')
            ->orWhere('deleted_at', '');

        $crm_groups = $query
            ->groupBy('shop_id', 'year', 'month')
            ->get();

        // Process the data
        $grouped_data = [];
        foreach ($crm_groups as $group) {
            $shop_id = $group->shop_id;
            $year = $group->year;
            $month = $group->month;

            if (!isset($grouped_data[$shop_id])) {
                $grouped_data[$shop_id] = [];
            }
            if (!isset($grouped_data[$shop_id][$year])) {
                $grouped_data[$shop_id][$year] = [];
            }
            if (!isset($grouped_data[$shop_id][$year][$month])) {
                $grouped_data[$shop_id][$year][$month] = [
                    'total_plan' => 0,
                    'total_plan_actual' => 0,
                    'data' => []
                ];
            }

            $grouped_data[$shop_id][$year][$month]['total_plan'] += $group->total_plan;
            $grouped_data[$shop_id][$year][$month]['total_plan_actual'] += $group->total_plan_actual;
            $grouped_data[$shop_id][$year][$month]['data'][] = $group;
        }

        // Flatten the grouped data for DataTables
        $flattened_data = [];
        foreach ($grouped_data as $shop_id => $years) {
            foreach ($years as $year => $months) {
                foreach ($months as $month => $data) {
                    foreach ($data['data'] as $visit) {
                        $flattened_data[] = [
                            'shop_id' => $shop_id,
                            'year' => $year,
                            'month' => $month,
                            'total_plan' => $data['total_plan'],
                            'total_plan_actual' => $data['total_plan_actual'],
                            'visit' => $visit,
                        ];
                    }
                }
            }
        }

        if (!Auth::user()->role_id == 1) {
            $shop_id = Auth::user()->shop_id;
            $flattened_data = array_filter($flattened_data, function ($item) use ($shop_id) {
                return $item['shop_id'] == $shop_id;
            });
        }

        return Datatables::of($flattened_data)
            ->addColumn('checkbox', function ($crm) {
                return view('admin.crm.partials.checkbox', ['crm' => $crm['visit']]);
            })
            ->addColumn('warehouse', function ($crm) {
                // Get the shop_id from the $crm instance
                $shop_id = $crm['shop_id'];

                // Query the Shop model to get the related shop
                $warehouse = Shop::where('id', $shop_id)->first();

                // Return the view with the fetched warehouse data
                return "<td>$warehouse->name</td>";
            })
            ->addColumn('month', function ($crm) {
                return view('admin.crm.partials.month', ['crm' => $crm['visit']]);
            })
            ->addColumn('year', function ($crm) {
                return view('admin.crm.partials.year', ['crm' => $crm['visit']]);
            })
            ->addColumn('total_plan', function ($crm) {
                return $crm['total_plan'];
            })
            ->addColumn('total_plan_actual', function ($crm) {
                return $crm['total_plan_actual'];
            })
            ->addColumn('success_rate', function ($crm) {
                $total_plan = $crm['total_plan'];
                $total_plan_actual = $crm['total_plan_actual'];
                return $total_plan > 0 ? ($total_plan_actual / $total_plan) * 100 . '%' : 0;
            })
            ->addColumn('status', function ($crm) {
                return view('admin.crm.partials.status', ['crm' => $crm['visit']]);
            })
            ->rawColumns(['checkbox', 'month', 'year', 'warehouse', 'status', 'total_plan', 'total_plan_actual', 'success_rate'])
            ->make(true);

    }

    public function getCRMsDataTables(Request $request)
    {
        if (Auth::user()->role_id === 8 || Auth::user()->role_id === 13) {
            $crms = $this->crm->all()->filter(function ($crm) {
                return $crm->shop_id == Auth::user()->shop_id;
            });

        } else if (Auth::user()->role_id === 1) {
            $crms = $this->crm->all();
        }

        return Datatables::of($crms)
            ->addColumn('checkbox', function ($crm) {
                return view('admin.crm.partials.checkbox', ['crm' => $crm]);
            })
            ->addColumn('date', function ($crm) {
                return view('admin.crm.partials.date', ['crm' => $crm]);
            })
            ->addColumn('month', function ($crm) {
                return view('admin.crm.partials.month', ['crm' => $crm]);
            })
            ->addColumn('year', function ($crm) {
                return view('admin.crm.partials.year', ['crm' => $crm]);
            })
            ->addColumn('warehouse', function ($crm) {
                return view('admin.crm.partials.warehouse', ['crm' => $crm]);
            })
            ->addColumn('client', function ($crm) {
                return view('admin.crm.partials.client', ['crm' => $crm]);
            })
            ->addColumn('picture', function ($crm) {
                return view('admin.crm.partials.picture', ['crm' => $crm]);
            })
            ->addColumn('verified_status', function ($crm) {
                return view('admin.crm.partials.verified_status', ['crm' => $crm]);
            })
            ->addColumn('created_at', function ($crm) {
                return view('admin.crm.partials.created_at', ['crm' => $crm]);
            })
            ->addColumn('created_by', function ($crm) {
                return view('admin.crm.partials.created_by', ['crm' => $crm]);
            })
            ->addColumn('verified_at', function ($crm) {
                return view('admin.crm.partials.verified_at', ['crm' => $crm]);
            })
            ->addColumn('verified_by', function ($crm) {
                return view('admin.crm.partials.verified_by', ['crm' => $crm]);
            })
            ->addColumn('updated_at', function ($crm) {
                return view('admin.crm.partials.updated_at', ['crm' => $crm]);
            })
            ->addColumn('updated_by', function ($crm) {
                return view('admin.crm.partials.updated_by', ['crm' => $crm]);
            })
            ->addColumn('options', function ($crm) {
                return view('admin.crm.partials.options', ['crm' => $crm]);
            })
            ->rawColumns(['checkbox', 'date', 'month', 'year', 'warehouse', 'client', 'picture', 'status', 'created_at', 'created_by', 'verified_status', 'verified_at', 'verified_by', 'updated_at', 'updated_by', 'options'])
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