<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Repositories\Logistic\LogisticRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;
use App\Models\Country;

class LogisticController extends Controller
{
    use Authorizable;

    private $model_name;

    private $logistic;

    /**
     * construct
     */
    public function __construct(LogisticRepository $logistic)
    {
        parent::__construct();

        $this->model_name = trans('app.model.logistic');

        $this->logistic = $logistic;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logistics = $this->logistic->all();

        $trashes = $this->logistic->trashOnly();

        return view('admin.logistic.index', compact('logistics'));
    }


    public function getLogistics(Request $request)
    {
        $logistics = $this->logistic->all();

        return Datatables::of($logistics)
            ->addColumn('checkbox', function ($logistic) {
                return '<td><input id="' . $logistic->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('name', function ($logistic) {
                return $logistic->name;
            })
            ->addColumn('phone', function ($logistic) {
                return $logistic->phone;
            })
            ->addColumn('email', function ($logistic) {
                return $logistic->email;
            })
            ->addColumn('pic_name', function ($logistic) {
                return $logistic->pic_name;
            })
            ->addColumn('pic_phone', function ($logistic) {
                return $logistic->pic_phone;
            })
            ->addColumn('pic_email', function ($logistic) {
                return $logistic->pic_email;
            })
            ->addColumn('option', function ($logistic) {
                return view('admin.logistic.options', compact('logistic'));
            })
            ->rawColumns(['checkbox', 'name', 'phone', 'email', 'pic_name', 'pic_phone', 'pic_email', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::get()
            ->pluck('name', 'id')
            ->toArray();
        return view('admin.logistic._create', compact('countries'));
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
        $this->logistic->store($request);
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
        $countries = Country::get()
            ->pluck('name', 'id')
            ->toArray();
        $logistic = $this->logistic->find($id);
        return view('admin.logistic._edit', compact('logistic', 'countries'));
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
        $this->logistic->update($request, $id);

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
        $request['deleted_by'] = Auth::user()->id;
        $this->logistic->update($request, $id);

        $this->logistic->trash($id);

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
        $this->logistic->restore($id);

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
        $this->logistic->destroy($id);

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
        $this->logistic->massTrash($request->ids);

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
        $this->logistic->massDestroy($request->ids);

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
        $this->logistic->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}