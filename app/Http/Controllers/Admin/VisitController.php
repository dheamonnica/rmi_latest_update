<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateVisitRequest;
use App\Http\Requests\Validations\UpdateVisitRequest;
use App\Repositories\Visit\VisitRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\CRM;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    use Authorizable;

    private $model_name;

    private $visit;

    /**
     * construct
     */
    public function __construct(VisitRepository $visit)
    {
        parent::__construct();

        $this->model_name = trans('app.model.visit');

        $this->visit = $visit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visits = $this->visit->all();

        $trashes = $this->visit->trashOnly();

        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        $years = CRM::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.visit.index', compact('trashes', 'visits', 'merchants', 'years'));
    }

    public function getVisitsTables(Request $request)
    {
        if (Auth::user()->role_id !== 1) {

            $visits = $this->visit->all(); // Retrieves all visits
            $shop_id = Auth::user()->shop_id;

            // Convert the result to a collection if not already
            $visits = collect($visits);

            $visits = $visits->filter(function ($item) use ($shop_id) {
                return $item->shop_id == $shop_id;
            });
        } else {
            $visits = $this->visit->all(); // Retrieves all visits
        }

        return Datatables::of($visits)
            ->addColumn('checkbox', function ($visit) {
                return view('admin.visit.partials.checkbox', compact('visit'));
            })
            ->addColumn('date', function ($visit) {
                return view('admin.visit.partials.date', compact('visit'));
            })
            ->addColumn('month', function ($visit) {
                return view('admin.visit.partials.month', compact('visit'));
            })
            ->addColumn('year', function ($visit) {
                return view('admin.visit.partials.year', compact('visit'));
            })
            ->addColumn('client', function ($visit) {
                return view('admin.visit.partials.client', compact('visit'));
            })
            ->addColumn('warehouse', function ($visit) {
                return view('admin.visit.partials.warehouse', compact('visit'));
            })
            ->addColumn('assignee', function ($visit) {
                return view('admin.visit.partials.assignee', compact('visit'));
            })
            ->addColumn('picture', function ($visit) {
                return view('admin.visit.partials.picture', compact('visit'));
            })
            ->addColumn('note', function ($visit) {
                return view('admin.visit.partials.note', compact('visit'));
            })
            ->addColumn('next_visit_date', function ($visit) {
                return view('admin.visit.partials.next_visit_date', compact('visit'));
            })
            ->addColumn('status', function ($visit) {
                return view('admin.visit.partials.status', compact('visit'));
            })
            ->addColumn('verified_at', function ($visit) {
                return view('admin.visit.partials.verified_by', compact('visit'));
            })
            ->addColumn('verified_by', function ($visit) {
                return view('admin.visit.partials.verified_by', compact('visit'));
            })
            ->addColumn('created_by', function ($visit) {
                return view('admin.visit.partials.created_by', compact('visit'));
            })
            ->addColumn('created_at', function ($visit) {
                return view('admin.visit.partials.created_at', compact('visit'));
            })
            ->addColumn('updated_at', function ($visit) {
                return view('admin.visit.partials.updated_at', compact('visit'));
            })
            ->addColumn('updated_by', function ($visit) {
                return view('admin.visit.partials.updated_by', compact('visit'));
            })
            ->addColumn('options', function ($visit) {
                return view('admin.visit.partials.options', compact('visit'));
            })

            ->rawColumns(['checkbox', 'options', 'date', 'month', 'year', 'warehouse', 'client', 'assignee', 'picture', 'note', 'next_visit_date', 'status', 'verified_at', 'verified_by', 'created_by', 'created_at', 'updated_at', 'updated_by'])
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
        $users = User::where('role_id', 8)->pluck('name', 'id')->toArray();

        return view('admin.visit._create', compact('customers', 'users'));
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    public function store(CreateVisitRequest $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->visit->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function setApprove(Request $request, $id)
    {

        $visit = $this->visit->find($id);

        $this->visit->updateStatusApprove($request, $visit);

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
        $visit = $this->visit->find($id);
        $users = User::get()->pluck('full_name', 'id')->toArray();
        $customers = Customer::get()->pluck('name', 'id')->toArray();

        return view('admin.visit._edit', compact('visit', 'users', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVisitRequest $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->visit->update($request, $id);

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
        $this->visit->trash($id);

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
        $this->visit->restore($id);

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
        $this->visit->destroy($id);

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
        $this->visit->massTrash($request->ids);

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
        $this->visit->massDestroy($request->ids);

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
        $this->visit->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}