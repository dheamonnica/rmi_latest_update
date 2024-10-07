<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Repositories\Loan\LoanRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;

class LoanController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $loan;

    /**
     * construct
     */
    public function __construct(LoanRepository $loan)
    {
        parent::__construct();

        $this->model_name = trans('app.model.loan');

        $this->loan = $loan;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = $this->loan->all();

        $trashes = $this->loan->trashOnly();

        return view('admin.loan.index', compact('loans'));
    }


    public function getLoans(Request $request)
    {
        $loans = $this->loan->all();

        return Datatables::of($loans)
            ->addColumn('checkbox', function ($loan) {
                return '<td><input id="' . $loan->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('created_by', function ($loan) {
                return $loan->getCreatedUsername->name;
            })
            ->addColumn('created_at', function ($loan) {
                return $loan->created_at;
            })
            ->addColumn('status', function ($loan) {
                return $loan->status == 0 ? 
                '<span class="label label-danger">NOT APPROVED</span>' : '<span class="label label-primary">APPROVED</span>';
            })
            ->addColumn('amount', function ($loan) {
                return $loan->amount;
            })
            ->addColumn('reason', function ($loan) {
                return $loan->reason;
            })
            ->addColumn('updated_at', function ($loan) {
                return $loan->updated_at;
            })
            ->addColumn('updated_by', function ($loan) {
                return $loan->updated_at ? $loan->getUpdatedUsername->name : '';
            })
            ->addColumn('approved_at', function ($loan) {
                return $loan->approved_at;
            })
            ->addColumn('approved_by', function ($loan) {
                return $loan->getApprovedUsername ? $loan->getApprovedUsername->name : '';
            })
            ->addColumn('option', function ($loan) {
                return view('admin.loan.partials.options', compact('loan'));
            })
            ->rawColumns(['checkbox', 'created_by', 'created_at', 'status', 'amount', 'reason', 'updated_at', 'updated_by', 'approved_at', 'approved_by', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.loan._create');
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
        $this->loan->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function setApprove(Request $request, $id)
    {

        $loan = $this->loan->find($id);

        $this->loan->updateStatusApprove($request, $loan);

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
        $loan = $this->loan->find($id);
        return view('admin.loan._edit', compact('loan'));
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
        $this->loan->update($request, $id);

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
        $this->loan->trash($id);

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
        $this->loan->restore($id);

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
        $this->loan->destroy($id);

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
        $this->loan->massTrash($request->ids);

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
        $this->loan->massDestroy($request->ids);

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
        $this->loan->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}