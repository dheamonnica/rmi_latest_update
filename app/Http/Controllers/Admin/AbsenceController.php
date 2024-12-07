<?php
namespace App\Http\Controllers\Admin;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use App\Repositories\Absence\AbsenceRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DB;
class AbsenceController extends Controller
{
    // use Authorizable;
    private $model_name;
    private $absence;
    /**
     * construct
     */
    public function __construct(AbsenceRepository $absence)
    {
        parent::__construct();
        $this->model_name = trans('app.model.absence');
        $this->absence = $absence;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $absences = $this->absence->all();
        $trashes = $this->absence->trashOnly();

        $branch_loc = User::where('shop_id', Auth::user()->shop_id)
            ->get()->first();
            if ($branch_loc->longitude === null && $branch_loc->latitude === null) {
                $branch_loc = User::where('id', Auth::user()->office_location_id)->get()->first();
            }
        return view('admin.absence.index', compact('absences', 'trashes', 'branch_loc'));
    }
    
    public function getAbsences(Request $request)
    {
        $absences = $this->absence->all();
        return Datatables::of($absences)
            ->addColumn('checkbox', function ($absence) {
                return '<td><input id="' . $absence->id . '" type="checkbox" class="massCheck"></td>';
            })
            ->addColumn('user_id', function ($absence) {
                return $absence->getUsername->name;
            })
            ->addColumn('address', function ($absence) {
                return $absence->address;
            })
            ->addColumn('clock_in', function ($absence) {
                return $absence->clock_in;
            })
            ->addColumn('clock_out', function ($absence) {
                return $absence->clock_out;
            })
            ->addColumn('branch_loc', function ($absence) {
                return $absence->getWarehouse->nice_name;
            })
            ->addColumn('address', function ($absence) {
                return $absence->address;
            })
            ->addColumn('total_hours', function ($absence) {
                return $absence->total_hours;
            })
            ->rawColumns(['checkbox', 'user_id', 'address', 'clock_in', 'clock_out', 'branch_loc', 'address', 'total_hours'])
            ->make(true);
    }

    public function checkIfUserHasClockIn(Request $request) {
        $absence = $this->absence->checkIfUserHasClockIn($request);
        return response()->json(['success' => $absence]);
    }

    public function checkIfUserHasClockOut(Request $request) {
        $absence = $this->absence->checkIfUserHasClockOut($request);
        return response()->json(['success' => $absence]);
    }

    public function clockOut(Request $request) {
        $absence = $this->absence->clockOut($request);
        return response()->json(['success' => $absence]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.absence._create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->absence->store($request);
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
        $absence = $this->absence->find($id);
        return view('admin.absence._edit', compact('absence'));
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
        $this->absence->update($request, $id);
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
        $this->absence->trash($id);
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
        $this->absence->restore($id);
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
        $this->absence->destroy($id);
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
        $this->absence->massTrash($request->ids);
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
        $this->absence->massDestroy($request->ids);
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
        $this->absence->emptyTrash($request);
        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }
        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}