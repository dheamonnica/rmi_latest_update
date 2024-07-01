<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Validations\CreateCatalogProductRequest;
use App\Http\Requests\Validations\UpdateCatalogProductRequest;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class CatalogProductController extends Controller
{
    use Authorizable;

    private $model;

    /**
     * construct
     */
    public function __construct()
    {
        // if (!is_catalog_enabled()) {
        //     abort(403);
        // }

        parent::__construct();

        $this->model = trans('app.model.product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->isFromPlatform()) {
            $trashes =  Product::onlyTrashed()->with('categories', 'featuredImage')->get();
        }

        $trashes = Product::mine()->onlyTrashed()->with('categories', 'featuredImage')->get();

        return view('admin.product.index', compact('trashes'));
    }

    // function will process the ajax request
    public function getProducts(Request $request)
    {
        $products = Product::with('categories', 'shop.logo', 'featureImage', 'image')
            ->withCount('inventories');

        // When accessing by a merchent user
        // if (Auth::user()->isFromMerchant()) {
        //     $products->mine();
        // }

        return Datatables::of($products)
        ->editColumn('checkbox', function ($product) {
            return view('admin.partials.actions.product.checkbox', compact('product'));
        })
        ->editColumn('image', function ($product) {
            return view('admin.partials.actions.product.image', compact('product'));
        })
        ->editColumn('name', function ($product) {
            return view('admin.partials.actions.product.name', compact('product'));
        })
        ->editColumn('general_name', function ($product) {
            return view('admin.partials.actions.product.general_name', compact('product'));
        })
        ->editColumn('licence', function ($product) {
            return view('admin.partials.actions.product.licence', compact('product'));
        })
        ->editColumn('selling_skuid', function ($product) {
            return view('admin.partials.actions.product.selling_skuid', compact('product'));
        })
        ->editColumn('purchase_price', function ($product) {
            return view('admin.partials.actions.product.purchase_price', compact('product'));
        })
        ->editColumn('min_price', function ($product) {
            return view('admin.partials.actions.product.min_price', compact('product'));
        })
        ->editColumn('max_price', function ($product) {
            return view('admin.partials.actions.product.max_price', compact('product'));
        })
        ->editColumn('inventories_count', function ($product) {
            return view('admin.partials.actions.product.inventories_count', compact('product'));
        })
        ->editColumn('added_by', function ($product) {
            return view('admin.partials.actions.product.added_by', compact('product'));
        })
        ->addColumn('option', function ($product) {
            return view('admin.partials.actions.product.options', compact('product'));
        })
        ->rawColumns(['image', 'name', 'licence', 'selling_skuid', 'purchase_price', 'min_price', 'max_price', 'inventories_count', 'added_by', 'option'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCatalogProductRequest $request)
    {
        $this->authorize('create', Product::class); // Check permission

        $product = Product::create($request->all());

        // Can have multiple images
        if ($request->hasFile('images')) {
            foreach ($request->images as $type => $file) {
                $product->saveImage($file, $type);
            }
        }

        // When got a single image
        if ($request->hasFile('image')) {
            $product->saveImage($request->image);
        }

        if ($request->has('category_list')) {
            $product->categories()->sync($request->input('category_list'));
        }

        if ($request->has('tag_list')) {
            $product->syncTags($product, $request->input('tag_list'));
        }

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        return response()->json($this->getJsonParams($product));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('inventories.shop')->find($id);

        $this->authorize('view', $product); // Check permission

        return view('admin.product._show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);

        $this->authorize('update', $product); // Check permission

        $preview = $product->previewImages();

        return view('admin.product.edit', compact('product', 'preview'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCatalogProductRequest $request, $id)
    {
        $product = Product::find($id);

        if ($request->hasFile('digital_file')) {
            $product->flushAttachments();
            $product->saveAttachments($request->file('digital_file'));
        }

        $this->authorize('update', $product); // Check permission

        $product->update($request->all());

        if ($request->input('delete_image')) {
            if (is_array($request->delete_image)) {
                foreach ($request->delete_image as $type => $value) {
                    $product->deleteImageTypeOf($type);
                }
            } else {
                $product->deleteImage();
            }
        }

        // Can have multiple images
        if ($request->hasFile('images')) {
            foreach ($request->images as $type => $file) {
                $product->updateImage($file, $type);
            }
        }

        // When got a single image
        if ($request->hasFile('image')) {
            $product->updateImage($request->image);
        }

        $request->session()->flash('success', trans('messages.updated', ['model' => $this->model]));

        return response()->json($this->getJsonParams($product));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, $id)
    {
        Product::findOrFail($id)->delete();

        return back()->with('success', trans('messages.trashed', ['model' => $this->model]));
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
        Product::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', trans('messages.restored', ['model' => $this->model]));
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
        $product = Product::onlyTrashed()->findOrFail($id);

        $product->detachTags($product->id, 'product');

        $product->flushImages();

        if ($product->hasFeedbacks()) {
            $product->flushFeedbacks();
        }

        $product->forceDelete();

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massTrash(Request $request)
    {
        Product::whereIn('id', $request->ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.trashed', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.trashed', ['model' => $this->model]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massRestore(Request $request)
    {
        Product::onlyTrashed()->whereIn('id', $request->ids)->restore();

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.restored', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.restored', ['model' => $this->model]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        $products = Product::onlyTrashed()->whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            $product->detachTags($product->id, 'product');

            $product->flushImages();

            if ($product->hasFeedbacks()) {
                $product->flushFeedbacks();
            }
        }

        Product::withTrashed()->whereIn('id', $request->ids)->forceDelete();

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * Empty the Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emptyTrash(Request $request)
    {
        $products = Product::onlyTrashed()->get();

        foreach ($products as $product) {
            $product->detachTags($product->id, 'product');

            $product->flushImages();

            if ($product->hasFeedbacks()) {
                $product->flushFeedbacks();
            }
        }

        Product::onlyTrashed()->forceDelete();

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * return json params to procceed the form
     *
     * @param  Product $product
     *
     * @return array
     */
    private function getJsonParams($product)
    {
        return [
            'id' => $product->id,
            'model' => 'product',
            'redirect' => route('admin.catalog.product.index'),
        ];
    }
}
