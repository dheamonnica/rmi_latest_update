<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreatePICRequest;
use App\Http\Requests\Validations\UpdatePICRequest;
use App\Models\PIC;
use App\Repositories\PIC\PICRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\Admin\DB;
use Illuminate\Support\Facades\DB;

class PICController extends Controller
{
    // use Authorizable;

    private $model_name;

    private $pic;

    /**
     * construct
     */
    public function __construct(PICRepository $pic)
    {
        parent::__construct();

        $this->model_name = trans('app.model.pic');

        $this->pic = $pic;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $data = DB::table('hospital_pic_customers')->select('name', 'value', 'phone', 'email', 'id', 'customer_id')->where('customer_id', $id)->get();
        $hospital = Customer::where('id', $id)
            ->get()
            ->first();

        $view = "";
        if (count($data) > 0) {
            $view = "admin.customer._edit_pic";
        } else {
            $view = "admin.customer._create_pic";
        }

        $positions = [
            'Director',
            'Manager Penunjang Med',
            'KP Perawat OK',
            'KP Perawat ICU',
            'KP Perawat IGD',
            'KP Perawat HE',
            'KP Perawat LAB',
            'KP Instalasi',
            'Purchasing 1',
            'Purchasing 2',
            'Purchasing 3',
            'Item / Teknik Medis',
            'Accounting',
            '',
            '',
            '',
        ];

        return view($view, compact('hospital', 'data', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePICRequest $request)
    {
        // Loop through each entry in the 'pic' array
        foreach ($request->input('pic') as $data) {
            if (isset($data['id'])) {
                $pic = PIC::where('id', $data['id'])
                    ->first();

                // If PIC exists, update the existing record
                $pic->update([
                    'name' => $data['name'],
                    'value' => $data['value'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'updated_by' => $data['updated_by'],
                    'updated_at' => now(),
                ]);
            } else {
                // If PIC doesn't exist, create a new record
                PIC::create([
                    'name' => $data['name'] ?? '',
                    'value' => $data['value'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'email' => $data['email'] ?? '',
                    'customer_id' => $data['customer_id'],
                    'created_by' => $data['created_by'],
                    'created_at' => now(),
                ]);
            }
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
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
        $this->pic->trash($id);

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
        $this->pic->restore($id);

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
        $this->pic->destroy($id);

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
        $this->pic->massTrash($request->ids);

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
        $this->pic->massDestroy($request->ids);

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
        $this->pic->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }
}