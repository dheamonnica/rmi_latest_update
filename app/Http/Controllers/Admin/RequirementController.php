<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateRequirementRequest;
use App\Http\Requests\Validations\UpdateRequirementRequest;
use App\Repositories\Requirement\RequirementRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;
use App\Models\Merchant;

class RequirementController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $requirement;

    /**
     * construct
     */
    public function __construct(RequirementRepository $requirement)
    {
        parent::__construct();

        $this->model_name = trans('app.model.requirement');

        $this->requirement = $requirement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchants = Merchant::whereNotNull('warehouse_name')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        return view('admin.budget.categories', compact('merchants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.budget.create_requirement');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequirementRequest $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->requirement->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function getRequirements(Request $request)
    {
        $requirements = $this->requirement->all();

        return Datatables::of($requirements)
            ->addColumn('checkbox', function ($requirement) {
                return "<td><input id=" . $requirement->id . " type='checkbox' class='massCheck'></td>";
            })
            ->addColumn('name', function ($requirement) {
                return $requirement->name;
            })
            ->addColumn('value', function ($requirement) {
                return $requirement->value . '%';
            })
            ->addColumn('warehouse', function ($requirement) {
                return $requirement->getWarehouse->name;
            })
            ->addColumn('created_by', function ($requirement) {
                return $requirement->getCreatedUsername->name;
            })
            ->addColumn('created_at', function ($requirement) {
                return $requirement->created_at;
            })
            ->addColumn('updated_by', function ($requirement) {
                if ($requirement->updated_at) {
                    return $requirement->getUpdatedUsername->name;
                }
            })
            ->addColumn('updated_at', function ($requirement) {
                return $requirement->updated_at;
            })
            ->addColumn('action', function ($requirement) {
                return view('admin.budget.partials.options_requirement', compact('requirement'));
            })
            ->rawColumns(['checkbox', 'name', 'value', 'warehouse', 'created_by', 'created_at', 'updated_by', 'updated_at', 'action'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $requirement = $this->requirement->find($id);
        return view('admin.budget.edit_requirement', compact('requirement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequirementRequest $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->requirement->update($request, $id);

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
        $this->requirement->trash($id);

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
        $this->requirement->restore($id);

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
        $this->requirement->destroy($id);

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
        $this->requirement->massTrash($request->ids);

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
        $this->requirement->massDestroy($request->ids);

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
        $this->requirement->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}