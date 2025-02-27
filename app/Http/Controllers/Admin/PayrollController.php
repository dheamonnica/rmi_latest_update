<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Repositories\Payroll\PayrollRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;

class PayrollController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $payroll;

    /**
     * construct
     */
    public function __construct(PayrollRepository $payroll)
    {
        parent::__construct();

        $this->model_name = trans('app.model.payroll');

        $this->payroll = $payroll;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payrolls = $this->payroll->all();

        $trashes = $this->payroll->trashOnly();

        return view('admin.payroll.index', compact('payrolls'));
    }


    public function getPayrolls(Request $request)
    {
        $payrolls = $this->payroll->all();

        return Datatables::of($payrolls)
            ->addColumn('checkbox', function ($payroll) {
                return '<td><input id="' . $payroll->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('position', function ($payroll) {
                return $payroll->position;
            })
            ->addColumn('grade', function ($payroll) {
                return $payroll->grade;
            })
            ->addColumn('sub_grade', function ($payroll) {
                return $payroll->sub_grade;
            })
            ->addColumn('level', function ($payroll) {
                return $payroll->level;
            })
            ->addColumn('take_home_pay', function ($payroll) {
                return $payroll->take_home_pay;
            })
            ->addColumn('basic_sallary', function ($payroll) {
                return $payroll->basic_sallary;
            })
            ->addColumn('operational_allowance', function ($payroll) {
                return $payroll->operational_allowance;
            })
            ->addColumn('position_allowance', function ($payroll) {
                return $payroll->position_allowance;
            })
            ->addColumn('child_education_allowance', function ($payroll) {
                return $payroll->child_education_allowance;
            })
            ->addColumn('transportation', function ($payroll) {
                return $payroll->transportation;
            })
            ->addColumn('quota', function ($payroll) {
                return $payroll->quota;
            })
            ->addColumn('created_at', function ($payroll) {
                return $payroll->created_at;
            })
            ->addColumn('created_by', function ($payroll) {
                return $payroll->getCreatedUsername->name;
            })
            ->addColumn('updated_at', function ($payroll) {
                return $payroll->updated_at;
            })
            ->addColumn('updated_by', function ($payroll) {
                return $payroll->updated_at ? $payroll->getUpdatedUsername->name : '';
            })
            ->addColumn('option', function ($payroll) {
                return view('admin.payroll.partials.options', compact('payroll'));
            })
            ->rawColumns(['checkbox', 'position', 'grade', 'sub_grade', 'level', 'take_home_pay', 'basic_sallary', 'operational_allowance', 'position_allowance', 'child_education_allowance', 'transportation', 'quota', 'created_at', 'created_by', 'updated_at', 'updated_by', 'options'])
            ->make(true);
    }

    public function report()
    {
        return view('admin.payroll.report');
    }

    public function getReportPayroll(Request $request)
    {
        $payrolls = Payroll::getReportPayroll();

        return Datatables::of($payrolls)
            ->addColumn('checkbox', function ($payroll) {
                return '<td><input id="' . $payroll->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('month_name', function ($payroll) {
                return $payroll->month_name;
            })
            ->addColumn('year', function ($payroll) {
                return $payroll->year;
            })
            ->addColumn('full_name', function ($payroll) {
                return $payroll->full_name;
            })
            ->addColumn('position', function ($payroll) {
                return $payroll->position;
            })
            ->addColumn('organization', function ($payroll) {
                return $payroll->organization;
            })
            ->addColumn('basic_sallary', function ($payroll) {
                return number_format($payroll->basic_sallary, 0, '.', '.');
            })
            ->addColumn('position_allowance', function ($payroll) {
                return number_format($payroll->position_allowance, 0, '.', '.');
            })
            ->addColumn('transportation', function ($payroll) {
                return number_format($payroll->transportation, 0, '.', '.');
            })
            ->addColumn('uang_oprational_harian', function ($payroll) {
                return number_format($payroll->uang_oprational_harian, 0, '.', '.');
            })
            ->addColumn('child_education_allowance', function ($payroll) {
                return number_format($payroll->child_education_allowance, 0, '.', '.');
            })
            ->addColumn('bonus_penjualan', function ($payroll) {
                return number_format($payroll->bonus_penjualan, 0, '.', '.');
            })
            ->addColumn('bonus', function ($payroll) {
                return number_format($payroll->bonus, 0, '.', '.');
            })
            ->addColumn('overtime', function ($payroll) {
                return number_format($payroll->overtime, 0, '.', '.');
            })
            ->addColumn('reimburse_etoll_bensin', function ($payroll) {
                return number_format($payroll->reimburse_etoll_bensin, 0, '.', '.');
            })
            ->addColumn('reimburse_pengobatan_sakit', function ($payroll) {
                return number_format($payroll->reimburse_pengobatan_sakit, 0, '.', '.');
            })
            ->addColumn('total_allowance', function ($payroll) {
                return number_format($payroll->total_allowance, 0, '.', '.');
            })
            ->addColumn('potongan_keterlambatan', function ($payroll) {
                return number_format($payroll->potongan_keterlambatan, 0, '.', '.');
            })
            ->addColumn('potongan_alpha', function ($payroll) {
                return number_format($payroll->potongan_alpha, 0, '.', '.');
            })
            ->addColumn('potongan_absensi', function ($payroll) {
                return number_format($payroll->potongan_absensi, 0, '.', '.');
            })
            ->addColumn('pinjaman', function ($payroll) {
                return number_format($payroll->pinjaman, 0, '.', '.');
            })
            ->addColumn('cicilan', function ($payroll) {
                return number_format($payroll->cicilan, 0, '.', '.');
            })
            ->addColumn('jaminan_pensiun_employee', function ($payroll) {
                return number_format($payroll->jaminan_pensiun_employee, 0, '.', '.');
            })
            ->addColumn('JHT_employee', function ($payroll) {
                return number_format($payroll->JHT_employee, 0, '.', '.');
            })
            ->addColumn('PPH21', function ($payroll) {
                return number_format($payroll->PPH21, 0, '.', '.');
            })
            ->addColumn('total_deduction', function ($payroll) {
                return number_format($payroll->total_deduction, 0, '.', '.');
            })
            ->addColumn('PPH21_payment', function ($payroll) {
                return number_format($payroll->PPH21_payment, 0, '.', '.');
            })
            ->addColumn('take_home_pay', function ($payroll) {
                return number_format($payroll->take_home_pay, 0, '.', '.');
            })
            ->addColumn('telekomunikasi', function ($payroll) {
                return number_format($payroll->telekomunikasi, 0, '.', '.');
            })

            ->rawColumns([
                'checkbox',
                'full_name',
                'position',
                'grade',
                'sub_grade',
                'level',
                'take_home_pay',
                'basic_sallary',
                'position_allowance',
                'transportation',
                'uang_oprational_harian',
                'child_education_allowance',
                'bonus_penjualan',
                'bonus',
                'overtime',
                'reimburse_etoll_bensin',
                'reimburse_pengobatan_sakit',
                'total_allowance',
                'potongan_keterlambatan',
                'potongan_alpha',
                'potongan_absensi',
                'pinjaman',
                'cicilan',
                'jaminan_pensiun_employee',
                'JHT_employee',
                'PPH21',
                'total_deduction',
                'PPH21_payment',
                'take_home_pay',
                'telekomunikasi',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payroll._create');
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
        $this->payroll->store($request);
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
        $payroll = $this->payroll->find($id);
        return view('admin.payroll._edit', compact('payroll'));
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
        $this->payroll->update($request, $id);

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
        $this->payroll->trash($id);

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
        $this->payroll->restore($id);

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
        $this->payroll->destroy($id);

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
        $this->payroll->massTrash($request->ids);

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
        $this->payroll->massDestroy($request->ids);

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
        $this->payroll->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}