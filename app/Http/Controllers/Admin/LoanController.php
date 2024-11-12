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
use App\Models\User;
use App\Models\LoanPayment;

class LoanController extends Controller
{
    use Authorizable;

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

    public function payment()
    {
        $loans = $this->loan->all();

        $trashes = $this->loan->trashOnly();

        return view('admin.loan.payment.index', compact('loans'));
    }

    public function report()
    {
        $loans = $this->loan->all();

        $trashes = $this->loan->trashOnly();

        return view('admin.loan.report', compact('loans'));
    }


    public function getLoans(Request $request)
    {
        $loans = $this->loan->all();

        return Datatables::of($loans)
            ->addColumn('checkbox', function ($loan) {
                return '<td><input id="' . $loan->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('id', function ($loan) {
                return $loan->id;
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
                return 'Rp. ' . number_format($loan->amount, 0, '.', '.');
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

    public function getLoanPayments(Request $request)
    {
        $loanPayments = LoanPayment::all();

        return Datatables::of($loanPayments)
            ->addColumn('checkbox', function ($loan_payment) {
                return '<td><input id="' . $loan_payment->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('user_id', function ($loan_payment) {
                return $loan_payment->getName->name;
            })
            ->addColumn('total_loan', function ($loan_payment) {
                return 'Rp. ' . number_format($loan_payment->total_loan, 0, '.', '.');
            })
            ->addColumn('amount', function ($loan_payment) {
                return 'Rp. ' . number_format($loan_payment->amount, 0, '.', '.');
            })
            ->addColumn('outstanding_balance', function ($loan_payment) {
                return 'Rp. ' . number_format($loan_payment->outstanding_balance, 0, '.', '.');
            })
            ->addColumn('created_at', function ($loan_payment) {
                return $loan_payment->created_at;
            })
            ->addColumn('created_by', function ($loan_payment) {
                return $loan_payment->getCreatedUsername->name;
            })
            ->addColumn('updated_at', function ($loan_payment) {
                return $loan_payment->updated_at;
            })
            ->addColumn('updated_by', function ($loan_payment) {
                return $loan_payment->updated_at ? $loan_payment->getUpdatedUsername->name : '';
            })
            ->addColumn('option', function ($loan_payment) {
                return view('admin.loan.partials.options_payment', compact('loan_payment'));
            })
            ->rawColumns(['checkbox', 'user_id', 'total_loan', 'amount', 'outstanding_balance', 'created_at', 'created_by', 'updated_at', 'updated_by', 'option'])
            ->make(true);
    }

    public function getLoanAndPaymentData(Request $request)
    {
        $id = $request->query('id');
        $results = Loan::getLoanAndPaymentData($id);

        return response()->json(['data' => $results]);
    }

    public function getDataLoanReportSecond(Request $request)
    {
        $results = Loan::getDataLoanReportSecond();

        return response()->json(['data' => $results]);
    }

    public function getDataLoanReportFirst(Request $request)
    {
        $loans = Loan::getDataLoanReportFirst();

        return Datatables::of($loans)
            ->addColumn('created_by', function ($loan) {
                return $loan->name;
            })
            ->addColumn('sum_amount_loan', function ($loan) {
                return 'Rp. ' . number_format($loan->sum_amount_loan, 0, '.', '.');
            })
            ->addColumn('sum_amount_loan_payment', function ($loan) {
                return 'Rp. ' . number_format($loan->sum_amount_loan_payment, 0, '.', '.');
            })
            ->addColumn('total_outstanding_balance', function ($loan) {
                $total_outstanding_balance = $loan->sum_amount_loan - $loan->sum_amount_loan_payment;
                return 'Rp. ' . number_format($total_outstanding_balance, 0, '.', '.');
            })
            ->addColumn('status', function ($loan) {
                if ($loan->sum_amount_loan_payment == $loan->sum_amount_loan) {
                    return '<span class="label label-primary">PAID</span>';
                } else {
                    return '<span class="label label-danger">UNPAID</span>';
                }
            })
            ->rawColumns(['created_by', 'sum_amount_loan', 'sum_amount_loan_payment', 'total_outstanding_balance', 'status'])
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

    public function createLoanPayment()
    {
        $loans = Loan::with('creator')->whereNull('deleted_at')->where('status', 1)->get();
        $users = $loans->pluck('creator')->unique('id')->pluck('full_name', 'id')->toArray();
        return view('admin.loan.payment._create', compact('users'));
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

    public function storeLoanPayment(Request $request)
    {
        $loanPayment = new LoanPayment;

        $loanPayment->amount = $request->input('amount');
        $loanPayment->total_loan = $request->input('total_loan');
        $loanPayment->outstanding_balance = $request->input('outstanding_balance');
        $loanPayment->is_paid = $request->input('outstanding_balance') == 0 ? 1 : 0;
        $loanPayment->user_id = $request->input('user_id'); 
        $loanPayment->created_at = now(); 
        $loanPayment->created_by = $request->input('created_by');
        $loanPayment->updated_by = $request->input('updated_by');
        $loanPayment->updated_at = $request->input('updated_at');

        // Save the LoanPayment instance to the database
        $loanPayment->save();

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

    public function editPaymentLoan($id)
    {
        $loan = LoanPayment::find($id);
        
        $loans = Loan::with('creator')->whereNull('deleted_at')->where('status', 1)->get();
        $users = $loans->pluck('creator')->unique('id')->pluck('full_name', 'id')->toArray();

        return view('admin.loan.payment._edit', compact('loan', 'users'));
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

    public function updatePaymentLoan(Request $request, $id)
    {
        $loanPayment = LoanPayment::find($id);

        $loanPayment->amount = $request->input('amount');
        $loanPayment->total_loan = $request->input('total_loan');
        $loanPayment->outstanding_balance = $request->input('outstanding_balance');
        $loanPayment->is_paid = $request->input('outstanding_balance') == 0 ? 1 : 0;
        $loanPayment->user_id = $request->input('user_id'); 
        $loanPayment->updated_by = $request->input('updated_by');
        $loanPayment->updated_at = $request->input('updated_at');

        $loanPayment->save();
        
        return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
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

    public function trashPaymentLoan(Request $request, $id)
    {
        $loanPayment = LoanPayment::find($id);
        $loanPayment->deleted_at = now();
        $loanPayment->deleted_by = Auth::user()->id;
        $loanPayment->updated_by = Auth::user()->id;

        $loanPayment->save();
        
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