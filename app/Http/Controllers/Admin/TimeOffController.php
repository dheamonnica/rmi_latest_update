<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\TimeOff;
use App\Repositories\TimeOff\TimeOffRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Support\Str;

class TimeOffController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $timeoff;

    /**
     * construct
     */
    public function __construct(TimeOffRepository $timeoff)
    {
        parent::__construct();

        $this->model_name = trans('app.model.timeoff');

        $this->timeoff = $timeoff;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timeoffs = $this->timeoff->all();

        $trashes = $this->timeoff->trashOnly();

        $years = range(2024, 2100);

        $merchants = Merchant::whereNotNull('warehouse_name')
            ->where('active', 1)
            ->whereNull('deleted_at')
            ->where('warehouse_name', 'like', '%warehouse%')
            ->get()
            ->pluck('warehouse_name', 'id')
            ->toArray();

        // Add the 'management' object to the array
        $merchants['management'] = 'Management';

        $timeoff_user_annual_leave = Timeoff::getUserTimeOffAnnualLeave(Auth::user()->id);

        $timeoff_user_sick_leave = Timeoff::getUserTimeOffSickLeave(Auth::user()->id);

        $timeoff_user_special_leave = Timeoff::getUserTimeOffSpecialLeave(Auth::user()->id);

        return view('admin.timeoff.index', compact('timeoffs', 'trashes', 'years', 'timeoff_user_annual_leave', 'timeoff_user_sick_leave', 'timeoff_user_special_leave', 'merchants'));
    }

    public function getTimeOff(Request $request)
    {
        $timeoffs = $this->timeoff->all();

        $leaveTypes = [
            'cuti_menikah' => 'Cuti Menikah',
            'cuti_menikahkan_anak' => 'Cuti Menikahkan Anak',
            'cuti_khitanan_anak' => 'Cuti Khitanan Anak',
            'cuti_baptis_anak' => 'Cuti Baptis Anak',
            'cuti_istri_melahirkan' => 'Cuti Istri Melahirkan/Keguguran',
            'cuti_keluarga_meninggal' => 'Cuti Keluarga Meninggal',
            'cuti_anggota_keluarga_meninggal' => 'Cuti Anggota Keluarga Dalam Satu Rumah Meninggal',
            'cuti_melahirkan' => 'Cuti Melahirkan',
            'cuti_haid' => 'Cuti Haid',
            'cuti_keguguran' => 'Cuti Keguguran',
            'cuti_ibadah_haji' => 'Cuti Ibadah Haji',
        ];

        return Datatables::of($timeoffs)
            ->addColumn('checkbox', function ($timeoff) {
                return '<td><input id="' . $timeoff->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('created_at', function ($timeoff) {
                return $timeoff->created_at;
            })
            ->addColumn('name', function ($timeoff) {
                return $timeoff->name;
            })
            ->addColumn('warehouse_id', function ($timeoff) {
                return $timeoff->warehouse_id ? $timeoff->getWarehouseName->name : 'Management';
            })
            ->addColumn('month', function ($timeoff) {
                return Carbon::parse($timeoff->start_date)->format('F');
            })
            ->addColumn('year', function ($timeoff) {
                return Carbon::parse($timeoff->start_date)->format('Y');
            })
            ->addColumn('created_by', function ($timeoff) {
                return $timeoff->getCreatedUsername->name;
            })
            ->addColumn('start_date', function ($timeoff) {
                return $timeoff->start_date;
            })
            ->addColumn('end_date', function ($timeoff) {
                return $timeoff->end_date;
            })
            ->addColumn('total_days', function ($timeoff) {
                return $timeoff->total_days;
            })
            ->addColumn('category', function ($timeoff) {
                return Str::title(str_replace('_', ' ', $timeoff->category));
            })
            ->addColumn('type', function ($timeoff) use ($leaveTypes) {
                return $leaveTypes[$timeoff->type] ?? $timeoff->type; // Fallback to the raw type if not found in the array
            })
            ->addColumn('notes', function ($timeoff) {
                return $timeoff->notes;
            })
            ->addColumn('status', function ($timeoff) {
                return $timeoff->status == 0 ?
                    '<span class="label label-danger">PENDING</span>' : '<span class="label label-primary">APPROVED</span>';
            })
            ->addColumn('picture', function ($timeoff) {
                return view('admin.timeoff.partials.picture', compact('timeoff'));
            })
            ->addColumn('approved_at', function ($timeoff) {
                return $timeoff->approved_at;
            })
            ->addColumn('approved_by', function ($timeoff) {
                return $timeoff->getApprovedUsername ? $timeoff->getApprovedUsername->name : '';
            })
            ->addColumn('updated_at', function ($timeoff) {
                return $timeoff->updated_at;
            })
            ->addColumn('updated_by', function ($timeoff) {
                return $timeoff->updated_at ? $timeoff->getUpdatedUsername->name : '';
            })
            ->addColumn('option', function ($timeoff) {
                return view('admin.timeoff.partials.options', compact('timeoff'));
            })
            ->rawColumns(['checkbox', 'created_at', 'created_by', 'name', 'warehouse_id', 'month', 'year', 'start_date', 'end_date', 'total_days', 'category', 'type', 'notes', 'status', 'approved_at', 'approved_by', 'updated_at', 'updated_by', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::whereNull('deleted_at')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $timeoff_user_annual_leave = Timeoff::getUserTimeOffAnnualLeave(Auth::user()->id);

        return view('admin.timeoff._create', compact('users', 'timeoff_user_annual_leave'));
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
        $this->timeoff->store($request);
        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    public function setApprove(Request $request, $id)
    {

        $timeoff = $this->timeoff->find($id);

        $this->timeoff->updateStatusApprove($request, $timeoff);

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
        $timeoff = $this->timeoff->find($id);
        return view('admin.timeoff._edit', compact('timeoff'));
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
        $this->timeoff->update($request, $id);

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
        $this->timeoff->trash($id);

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
        $this->timeoff->restore($id);

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
        $this->timeoff->destroy($id);

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
        $this->timeoff->massTrash($request->ids);

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
        $this->timeoff->massDestroy($request->ids);

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
        $this->timeoff->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}