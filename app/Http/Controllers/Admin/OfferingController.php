<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateOfferingRequest;
use App\Http\Requests\Validations\UpdateOfferingRequest;
use App\Repositories\Offering\OfferingRepository;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\Inventory;
use App\Models\Product;

class OfferingController extends Controller
{
    use Authorizable;

    private $model_name;

    private $offering;

    /**
     * construct
     */
    public function __construct(OfferingRepository $offering)
    {
        parent::__construct();

        $this->model_name = trans('app.model.offering');

        $this->offering = $offering;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::get()->pluck('name', 'id')->toArray();

        $offerings = $this->offering->all();

        $trashes = $this->offering->trashOnly();

        return view('admin.offering.index', compact('products', 'offerings', 'trashes'));
    }

    public function getOfferings(Request $request)
    {
        $offerings = $this->offering->all();

        return Datatables::of($offerings)
            ->addColumn('checkbox', function ($offering) {
                return view('admin.offering.partials.checkbox', compact('offering'));
            })
            ->addColumn('product', function ($offering) {
                return view('admin.offering.partials.product', compact('offering'));
            })
            ->addColumn('small_quantity_price', function ($offering) {
                return view('admin.offering.partials.small_quantity_price', compact('offering'));
            })
            ->addColumn('medium_quantity_price', function ($offering) {
                return view('admin.offering.partials.medium_quantity_price', compact('offering'));
            })
            ->addColumn('large_quantity_price', function ($offering) {
                return view('admin.offering.partials.large_quantity_price', compact('offering'));
            })
            ->addColumn('created_at', function ($offering) {
                return view('admin.offering.partials.created_at', compact('offering'));
            })
            ->addColumn('created_by', function ($offering) {
                return view('admin.offering.partials.created_by', compact('offering'));
            })
            ->addColumn('company_name', function ($offering) {
                return view('admin.offering.partials.company_name', compact('offering'));
            })
            ->addColumn('email', function ($offering) {
                return view('admin.offering.partials.email', compact('offering'));
            })
            ->addColumn('phone', function ($offering) {
                return view('admin.offering.partials.phone', compact('offering'));
            })
            ->addColumn('updated_at', function ($offering) {
                return view('admin.offering.partials.updated_at', compact('offering'));
            })
            ->addColumn('updated_by', function ($offering) {
                return view('admin.offering.partials.updated_by', compact('offering'));
            })
            ->addColumn('status', function ($offering) {
                return view('admin.offering.partials.status', compact('offering'));
            })
            ->addColumn('option', function ($offering) {
                return view('admin.offering.partials.options', compact('offering'));
            })

            ->rawColumns([
                'checkbox',
                'product',
                'small_quantity',
                'small_quantity_price',
                'medium_quantity_price',
                'large_quantity_price',
                'created_at',
                'company_name',
                'email',
                'phone',
                'created_by',
                'updated_at',
                'updated_by',
                'option'
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
        $product = Product::get()->pluck('name', 'id')->toArray();
        return view('admin.offering._create', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOfferingRequest $request)
    {
        $this->offering->store($request);

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     return redirect()->route('admin.offering.index');
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::get()->pluck('name', 'id')->toArray();

        $offering = $this->offering->find($id);

        return view('admin.offering._edit', compact('offering', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOfferingRequest $request, $id)
    {
        $this->offering->update($request, $id);

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
        $this->offering->trash($id);

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
        $this->offering->restore($id);

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
        $this->offering->destroy($id);

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
        $this->offering->massTrash($request->ids);

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
        $this->offering->massDestroy($request->ids);

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
        $this->offering->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    public function setApprove(Request $request, $id)
    {

        $offering = $this->offering->find($id);

        $this->offering->updateStatusApprove($request, $offering);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }
}
