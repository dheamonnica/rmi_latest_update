<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\Overtime;
use App\Repositories\Overtime\OvertimeRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;

class OvertimeController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $overtime;

    /**
     * construct
     */
    public function __construct(OvertimeRepository $overtime)
    {
        parent::__construct();

        $this->model_name = trans('app.model.overtime');

        $this->overtime = $overtime;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overtimes = $this->overtime->all();

        $trashes = $this->overtime->trashOnly();

        return view('admin.overtime.index', compact('overtimes'));
    }


    public function getOvertimes(Request $request)
    {
        $overtimes = $this->overtime->all();

        return Datatables::of($overtimes)
            ->addColumn('checkbox', function ($overtime) {
                return '<td><input id="' . $overtime->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('user_id', function ($overtime) {
                return $overtime->getCreatedBy->name;
            })
            ->addColumn('start_time', function ($overtime) {
                return $overtime->start_time;
            })
            ->addColumn('end_time', function ($overtime) {
                return $overtime->end_time;
            })
            ->addColumn('status', function ($overtime) {
                return $overtime->status == 0 ? 
                '<span class="label label-danger">NOT APPROVED</span>' : '<span class="label label-primary">APPROVED</span>';
            })
            ->addColumn('created_at', function ($overtime) {
                return $overtime->created_at;
            })
            ->addColumn('created_by', function ($overtime) {
                return $overtime->getCreatedUsername->name;
            })
            ->addColumn('approved_at', function ($overtime) {
                return $overtime->approved_at;
            })
            ->addColumn('approved_by', function ($overtime) {
                return $overtime->getApprovedUsername ? $overtime->getApprovedUsername->name : '';
            })
            ->addColumn('updated_at', function ($overtime) {
                return $overtime->updated_at;
            })
            ->addColumn('updated_by', function ($overtime) {
                return $overtime->updated_at ? $overtime->getUpdatedUsername->name : '';
            })
            ->addColumn('option', function ($overtime) {
                return view('admin.overtime.partials.options', compact('overtime'));
            })
            ->rawColumns(['checkbox', 'user_id', 'start_time', 'end_time', 'status', 'created_at', 'created_by', 'approved_at', 'approved_by', 'updated_at', 'updated_by', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.overtime._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->overtime->store($request);
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
        $overtime = $this->overtime->find($id);
        return view('admin.overtime._edit', compact('overtime'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->overtime->update($request, $id);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function setApprove(Request $request, $id)
    {

        $overtime = $this->overtime->find($id);

        $this->overtime->updateStatusApprove($request, $overtime);

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
        $this->overtime->trash($id);

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
        $this->overtime->restore($id);

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
        $this->overtime->destroy($id);

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
        $this->overtime->massTrash($request->ids);

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
        $this->overtime->massDestroy($request->ids);

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
        $this->overtime->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}