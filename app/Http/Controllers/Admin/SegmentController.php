<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateSegmentRequest;
use App\Http\Requests\Validations\UpdateSegmentRequest;
use App\Repositories\Segment\SegmentRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;
use App\Models\Merchant;

class SegmentController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $segment;

    /**
     * construct
     */
    public function __construct(SegmentRepository $segment)
    {
        parent::__construct();

        $this->model_name = trans('app.model.segment');

        $this->segment = $segment;
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

        return view('admin.budget.config', compact('merchants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.budget.create_config');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSegmentRequest $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['created_at'] = date('Y-m-d G:i:s');
        $this->segment->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function getSegments(Request $request)
    {
        $segments = $this->segment->all();

        return Datatables::of($segments)
            ->addColumn('checkbox', function ($segment) {
                return "<td><input id=" . $segment->id . " type='checkbox' class='massCheck'></td>";
            })
            ->addColumn('name', function ($segment) {
                return $segment->name;
            })
            ->addColumn('value', function ($segment) {
                return $segment->value . '%';
            })
            ->addColumn('warehouse', function ($segment) {
                return $segment->getWarehouse->name;
            })
            ->addColumn('created_by', function ($segment) {
                return $segment->getCreatedUsername->name;
            })
            ->addColumn('created_at', function ($segment) {
                return $segment->created_at;
            })
            ->addColumn('updated_by', function ($segment) {
                if ($segment->updated_at) {
                    return $segment->getUpdatedUsername->name;
                }
            })
            ->addColumn('updated_at', function ($segment) {
                return $segment->updated_at;
            })
            ->addColumn('action', function ($segment) {
                return view('admin.budget.partials.options_segment', compact('segment'));
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
        $segment = $this->segment->find($id);
        return view('admin.budget.edit_config', compact('segment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSegmentRequest $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request['updated_at'] = date('Y-m-d G:i:s');
        $this->segment->update($request, $id);

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
        $this->segment->trash($id);

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
        $this->segment->restore($id);

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
        $this->segment->destroy($id);

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
        $this->segment->massTrash($request->ids);

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
        $this->segment->massDestroy($request->ids);

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
        $this->segment->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}