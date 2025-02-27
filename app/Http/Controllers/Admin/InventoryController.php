<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Inventory;
use App\Helpers\ListHelper;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Inventory\InventoryRepository;
use App\Http\Requests\Validations\AddInventoryRequest;
use App\Http\Requests\Validations\CreateInventoryRequest;
use App\Http\Requests\Validations\UpdateInventoryRequest;
use App\Http\Requests\Validations\CreateInventoryWithVariantRequest;
use App\Models\Shop;
use App\Models\StockTransfer;
use App\Models\User;
use DB;

class InventoryController extends Controller
{
    private $model;

    private $inventory;

    /**
     * construct
     */
    public function __construct(InventoryRepository $inventory)
    {
        parent::__construct();

        $this->model = trans('app.model.inventory');

        $this->inventory = $inventory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type = null)
    {
        $trashes = Inventory::withCount('variants')
            ->onlyTrashed()
            ->where('parent_id', null)
            ->latest();

        if (!Auth::user()->isFromPlatform()) {
            $trashes = $trashes->mine();
        }

        // Get auction items only
        if ($type == 'auction') {
            $trashes = $trashes->where('auctionable', 1);
        }

        $trashes = $trashes->get();

        if ($type == 'digital') {
            return view('admin.inventory.index_digital', compact('trashes'));
        }

        if ($type == 'auction') {
            return view('auction::admin.index', compact('trashes'));
        }

        return view('admin.inventory.index', compact('trashes'));
    }

    // Function will process the ajax request to fetch data
    public function getInventory(Request $request, $status = 'active', $type = null)
    {
        $inventory = Inventory::with('product', 'image')
            ->withCount('variants')
            ->where('parent_id', null)
            ->latest();

        if (!Auth::user()->isFromPlatform()) {
            $inventory = $inventory->mine();
        }

        if ($type == 'auction') {        // Get only auction items
            $inventory = $inventory->withCount('bids')->auction();
        }

        if ($status == 'active') {
            $inventory = $inventory->active();
        } elseif ($status == 'inactive') {
            $inventory = $inventory->inActive();
        } elseif ($status == 'outOfStock') {
            $inventory = $inventory->stockOut();
        } elseif ($status == 'stockTransfer') {
            $inventory = $inventory->stockTransfer();
        }

        $inventory = $inventory->get();

        // Separate products by type when catalog mode is enabled
        $inventory = $inventory->filter(function ($item) use ($type) {
            if ($type === 'digital') {
                return  $item->product->downloadable;      // Include only digital products
            }

            if ($type === 'physical') {
                return  !$item->product->downloadable;      // Include only physical products
            }

            return true;
        });

        $data = Datatables::of($inventory)
            ->editColumn('checkbox', function ($inventory) {
                return view('admin.inventory.partials.checkbox', compact('inventory'));
            })
            ->editColumn('image', function ($inventory) use ($status) {
                return view('admin.inventory.partials.image', compact('inventory', 'status'));
            })
            ->editColumn('quantity', function ($inventory) {
                return view('admin.inventory.partials.quantity', compact('inventory'));
            })
            ->editColumn('sku', function ($inventory) {
                return view('admin.inventory.partials.sku', compact('inventory'));
            })
            ->editColumn('title', function ($inventory) use ($type) {
                return view('admin.inventory.partials.title', compact('inventory', 'type'));
            })
            ->editColumn('condition', function ($inventory) {
                return view('admin.inventory.partials.condition', compact('inventory'));
            })
            ->editColumn('download_limit', function ($inventory) {
                return view('admin.inventory.partials.download_limit', compact('inventory'));
            });

        $rawColumns = ['image', 'sku', 'title', 'checkbox', 'option', 'download_limit'];

        if ($type == 'auction') {
            $data = $data->editColumn('base_price', function ($inventory) {
                return view('auction::admin._price', compact('inventory'));
            })->addColumn('option', function ($inventory) {
                return view('auction::admin._options', compact('inventory'));
            });

            $rawColumns[] = 'base_price';
        } else {

            $data = $data->editColumn('sale_price', function ($inventory) {
                return view('admin.inventory.partials.price', compact('inventory'));
            })->addColumn('option', function ($inventory) {
                return view('admin.inventory.partials.options', compact('inventory'));
            });

            if ($status == 'stockTransfer') {
                $data = $data->editColumn('option', function ($inventory) {
                    $stock_transfer = StockTransfer::where('id',$inventory->stock_transfer_id)->first();
                    return view('admin.inventory.partials.stock_transfer_options', compact('stock_transfer'));
                });

                $data = $data->editColumn('status', function ($inventory) {
                    return get_stock_transfer_status_name((int) $inventory->status);
                });

                $data = $data->editColumn('updated_by', function ($inventory) {
                    $user = User::find($inventory->updated_by);
                    return $inventory->updated_by ? $user->pic_name .' ('.$user->warehouse_name.')': '-';
                });

                $data = $data->editColumn('approve_by', function ($inventory) {
                    $user = User::find($inventory->approve_by);
                    return $inventory->approve_by ? $user->pic_name.' ('.$user->warehouse_name.')' : '-';
                });
            }
            
            $rawColumns[] = 'sale_price';
        }

        $rawColumns[] = 'option';

        if (config('system_settings.show_item_conditions')) {
            $data = $data->editColumn('condition', function ($inventory) {
                return view('admin.inventory.partials.condition', compact('inventory'));
            });

            $rawColumns[] = 'condition';
        }

        return $data->rawColumns($rawColumns)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function setVariant(AddInventoryRequest $request, Product $product)
    {
        $attributes = ListHelper::getAttributesBy($product);

        return view('admin.inventory._set_variant', compact('product', 'attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(AddInventoryRequest $request, $id)
    {
        if (!$request->user()->shop->canAddMoreInventory()) {
            return redirect()->route('admin.stock.inventory.index')
                ->with('error', trans('messages.cant_add_more_inventory'));
        }

        // $inStock = $this->inventory->checkInventoryExist($id);

         // if ($inStock) {
         //     //change into if exist the produst will create new but different expired date
         //     return redirect()->route('admin.stock.inventory.edit', $inStock->id)
         //         ->with('warning', trans('messages.inventory_exist'));
         // }

        $product = Product::with('categories.attrsList.attributeValues')->findOrFail($id);

        $attributes = ListHelper::getAttributesBy($product);

        // When packaging module available
        if (is_incevio_package_loaded('packaging')) {
            $packagings = ListHelper::packagings();

            return view('admin.inventory.create', compact('product', 'attributes', 'packagings'));
        }

        return view('admin.inventory.create', compact('product', 'attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addWithVariant(AddInventoryRequest $request, $id)
    {
        if (!$request->user()->shop->canAddMoreInventory()) {
            return redirect()->route('admin.stock.inventory.index')
                ->with('error', trans('messages.cant_add_more_inventory'));
        }

        $variants = $this->inventory->confirmAttributes($request->except('_token'));

        $combinations = generate_combinations($variants);

        $attributes = $this->inventory->getAttributeList(array_keys($variants));

        $product = $this->inventory->findProduct($id);

        if (is_incevio_package_loaded('packaging')) {
            $packagings = ListHelper::packagings();

            return view('admin.inventory.createWithVariant', compact('combinations', 'attributes', 'product', 'packagings'));
        }

        return view('admin.inventory.createWithVariant', compact('combinations', 'attributes', 'product'));
    }

    /**
     * Add a product to inventory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInventoryRequest $request)
    {
        $this->authorize('create', \App\Models\Inventory::class); // Check permission

        $inventory = $this->inventory->store($request);

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        return response()->json($this->getJsonParams($inventory));
    }

    /**
     * Add inventory with variants.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWithVariant(CreateInventoryWithVariantRequest $request)
    {
        if (json_decode($request->input('product')) == null) { //If the json string is invalid
            $request->merge([
                'product' => $this->makeStringJsonCompatible($request->product)
            ]);
        }

        $this->inventory->storeWithVariant($request);

        return redirect()->route('admin.stock.inventory.index')
            ->with('success', trans('messages.created', ['model' => $this->model]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventory = $this->inventory->find($id);

        return view('admin.inventory._show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inventory = $this->inventory->find($id);

        $inventoryVariant = Inventory::where('parent_id', $inventory->id)->get();

        // dd($inventory->toArray());
        // $client = new \Incevio\Package\Ebay\SDK\Ebay();
        // $response = $client->createOrUpdateItem($inventory);
        // $response = $client->getItemFromEbay($inventory->sku);
        // dd(json_decode($response->getBody()->getContents()));

        $product = $this->inventory->findProduct($inventory->product_id);

        $preview = $inventory->previewImages();

        $attributes = ListHelper::getAttributesBy($product);

        if (is_incevio_package_loaded('packaging')) {
            $packagings = ListHelper::packagings();

            return view('admin.inventory.edit', compact('inventory', 'inventoryVariant', 'product', 'preview', 'attributes', 'packagings'));
        }

        return view('admin.inventory.edit', compact('inventory', 'inventoryVariant', 'product', 'preview', 'attributes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editQtt($id)
    {
        $inventory = $this->inventory->find($id);

        $this->authorize('update', $inventory); // Check permission

        return view('admin.inventory._editQtt', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInventoryRequest $request, $id)
    {
        // $inventory = $this->inventory->update($request, $id);

        $inventory = Inventory::find($id);

        // Skip the permission checking for platform users when for inspectable item update
        if (!Auth::user()->isFromPlatform()) {
            $this->authorize('update', $inventory); // Check permission
        }

        if ($request->hasFile('digital_file')) {
            $inventory->flushAttachments();
            $inventory->saveAttachments($request->file('digital_file'));
        }

        if ($request->input('delete_image')) {
            if (is_array($request->delete_image)) {
                foreach ($request->delete_image as $type => $value) {
                    $inventory->deleteImageTypeOf($type);
                }
            } else {
                $inventory->deleteImage();
            }
        }

        // Can have multiple images
        if ($request->hasFile('images')) {
            foreach ($request->images as $type => $file) {
                $inventory->updateImage($file, $type);
            }
        }

        // When got a single image
        if ($request->hasFile('image')) {
            $inventory->updateImage($request->image);
        }

        // dd($request->all());
        $inventory = $this->inventory->update($request, $id);

        $commonInfo = [
            'title' => $request->title ?? $request->name,
            'warehouse_id' => $request->warehouse_id,
            'brand' => $request->brand,
            'condition' => $request->condition,
            'condition_note' => $request->condition_note,
            'key_features' => $request->key_features,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price,
            'available_form' => $request->available_form,
            'offer_price' => $request->offer_price,
            'offer_start' => $request->offer_start,
            'offer_end' => $request->offer_end,
            'shipping_weight' => $request->shipping_weight,
            'free_shipping' => $request->free_shipping,
            'available_from' => $request->available_from,
            'expiry_date' => $request->expiry_date,
            'min_order_quantity' => $request->min_order_quantity,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'download_limit' => $request->download_limit,
            'supplier_id' => $request->supplier_id,
        ];

        $variant_skus = $request->get('variant_skus');
        $variant_quantities = $request->get('variant_quantities');
        $variant_prices = $request->get('variant_prices');
        $variant_images = $request->file('variant_images');

        $oldVariants = Inventory::where('parent_id', $id)->get();

        if (isset($oldVariants)) {
            foreach ($oldVariants as $oldVariant) {
                if (!in_array($oldVariant->sku, $variant_skus)) {
                    $oldVariant->delete();
                }
            }
        }

        if (isset($variant_skus)) {
            foreach ($variant_skus as $key => $variant_sku) {
                $dynamicInfo = [
                    'sku' => $variant_sku,
                    'stock_quantity' => $variant_quantities[$key],
                    'sale_price' => $variant_prices[$key],
                ];

                // Merge the common info and dynamic info to data array
                $data = array_merge($dynamicInfo, $commonInfo);

                // Insert the record
                $inventory = Inventory::find($key);
                $inventory->update($data);

                // Save Images
                if (isset($variant_images[$key])) {
                    $inventory->saveImage($variant_images[$key]);
                }
            }
        }

        $request->session()->flash('success', trans('messages.updated', ['model' => $this->model]));

        return response()->json($this->getJsonParams($inventory));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQtt(Request $request, $id)
    {
        $inventory = $this->inventory->updateQtt($request, $id);

        return response('success', 200);
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
        $childInventoryIds = Inventory::where('parent_id', $id)->pluck('id')->toArray();

        if (isset($childInventoryIds)) {
            foreach ($childInventoryIds as $inventoryId) {
                $this->inventory->trash($inventoryId);
            }
        }

        $this->inventory->trash($id);

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
        $childInventoryIds = Inventory::where('parent_id', $id)->withTrashed()->pluck('id')->toArray();

        if (isset($childInventoryIds)) {
            foreach ($childInventoryIds as $inventoryId) {
                $this->inventory->restore($inventoryId);
            }
        }

        $this->inventory->restore($id);

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
        $childInventoryIds = Inventory::where('parent_id', $id)->withTrashed()->pluck('id')->toArray();

        if (isset($childInventoryIds)) {
            foreach ($childInventoryIds as $inventoryId) {
                $this->inventory->destroy($inventoryId);
            }
        }

        $this->inventory->destroy($id);

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
        $parentIds = $request->ids;

        foreach ($parentIds as $parentId) {
            $childInventoryIds = Inventory::where('parent_id', $parentId)->pluck('id')->toArray();
            foreach ($childInventoryIds as $inventoryId) {
                array_push($parentIds, $inventoryId);
            }
        }

        $this->inventory->massTrash($parentIds);

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
    public function massDestroy(Request $request)
    {
        $parentIds = $request->ids;

        foreach ($parentIds as $parentId) {
            $childInventoryIds = Inventory::where('parent_id', $parentId)->pluck('id')->toArray();
            foreach ($childInventoryIds as $inventoryId) {
                array_push($parentIds, $inventoryId);
            }
        }

        $this->inventory->massDestroy($parentIds);

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
        $this->inventory->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * Add single variant
     *
     * @param \Illuminate\Http\Request $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\Response
     */
    public function singleVariantForm(Request $request, Inventory $inventory)
    {
        $product = $inventory->product;

        $attributes = ListHelper::getAttributesBy($product);

        $productAttributeIds = $attributes->pluck('id');

        return view('admin.inventory.add_variant', compact('product', 'inventory', 'attributes', 'productAttributeIds'));
    }

    // public function saveSingleVariant(CreateProductVariantRequest $request, Product $product)
    public function saveSingleVariant(Request $request, Inventory $inventory)
    {
        $request->validate([
            'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' .  auth()->user()->merchantId()
        ]);

        $attributes = $request->get('attributes');

        // Verify variant uniqueness
        // if (!$this->verifyVariantUniqueness($product, $attributes)) {
        //     return back()->with('error', trans('responses.variant_unique'))->withInput();
        // }

        // Create the variant
        $product = $inventory->product;


        $data = [
            'title' => $request->get('title'),
            // 'available_from' => $product->available_from == $request->get('available_from') ? $request->get('available_from') : Null,
            // 'requires_shipping' => $request->get('requires_shipping'),
            // 'downloadable' => $request->get('downloadable'),
            'product_id' => $product->id,
            'condition' => $inventory->condition,
            'sku' => $request->get('sku'),
            'parent_id' => $inventory->id,
            'shop_id' => $inventory->shop_id,
            'warehouse_id' => $inventory->warehouse_id,
            'brand' => $inventory->brand,
            'supplier_id' => $inventory->supplier_id,
            'condition_note' => $inventory->condition_note,
            'stock_quantity' => $request->get('stock_quantity'),
            'shipping_weight' => $inventory->shipping_weight,
            'free_shipping' => $inventory->free_shipping,
            'available_from' => $inventory->available_from->format('Y-m-d h:i a'),
            'slug' => $inventory->slug . '-' . $request->get('sku'),
            'min_order_quantity' => $inventory->min_order_quantity,
            'user_id' => $request->user()->id,
            'sale_price' => $request->get('sale_price'),
        ];

        $variant = Inventory::create($data);

        $this->setAttributes($variant, $attributes);

        if ($request->hasFile('image')) {
            $variant->saveImage($request->file('image')); // Save image
            // Link the vriant to this image
            // $variant->image_id = $image->id;
            $variant->save();
        }

        $request->session()->flash('success', trans('messages.created', ['model' => trans('app.variant')]));

        return redirect()->route('admin.stock.inventory.edit', $inventory);
    }

    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param obj $inventory
     * @param array $attributes
     */
    public function setAttributes($inventory, $attributes)
    {
        $attributes = array_filter($attributes ?? []);        // remove empty elements

        $temp = [];
        foreach ($attributes as $attribute_id => $attribute_value_id) {
            $temp[$attribute_id] = ['attribute_value_id' => $attribute_value_id];
        }

        if (!empty($temp)) {
            $inventory->attributes()->sync($temp);
        }

        return true;
    }

    /**
     * return json params to procceed the form
     *
     * @param  Product $product
     *
     * @return array
     */
    private function getJsonParams($inventory)
    {
        if (Auth::user()->isFromPlatform()) {
            $route = route('admin.inspector.inspectables');
        } elseif ($inventory->product->downloadable) {
            $route = route('admin.stock.inventory.index', 'digital');
        } elseif ($inventory->auctionable) {
            $route = route('admin.stock.inventory.index', 'auction');
        } else {
            $route = route('admin.stock.inventory.index', 'physical');
        }

        return [
            'id' => $inventory->id,
            'model' => 'inventory',
            'redirect' => $route,
        ];
    }

    /**
     * Make string json compatible by removing any unescaped double quotes in the passed json string.
     * As the string contains font-family property, where the font-family value may contain double quotes.
     * Without escaping the double quotes, the json string will be invalid due to encoding issues.
     * @param $string
     * @return string
     */
    private function makeStringJsonCompatible($string)
    {
        $regex = "/font-family: (.*?);/"; // regex to get only font-family property value

        // Replace font-family property value with escaped double quotes
        $filtered_string = preg_replace_callback($regex, function ($matches) {
            $font_family = $matches[1];
            $font_family_escaped = str_replace('"', '"', $font_family); // Escape unescaped quotation marks

            return "font-family: $font_family_escaped;";
        }, $string);

        return $filtered_string;
    }

    /**
     * Show stock transfer form
     *
     * @return \Illuminate\Http\Response
     */
    public function showForm()
    {
        return view('admin.inventory._stock_transfer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchWarehouse()
    {
        return view('admin.order._search_warehouse');
    }

    public function stockTransferWarehouse(Request $request)
    {
        // dump($request->input('warehouse_id'));
        $data['shop_arrival_id'] = (int) $request->input('warehouse_id');
        $data['shop_depature_id'] = (int) Auth::user()->shop_id;
        $data['shop_depature'] = Shop::where('id', (int) Auth::user()->shop_id)->first();
        $data['shop_arrival'] = Shop::where('id', (int) $request->input('warehouse_id'))->first();

        return view('admin.inventory._stock_transfer_form', $data);
    }

    public function storeStockTransfer(Request $request)
    {
        // dd($request->all());

        $request->validate([
            //'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' .  auth()->user()->merchantId()
        ]);


       $inventory = $this->inventory->storeStockTransfer($request);

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        return redirect()->route('admin.stock.inventory.index');

    //    return response()->json($this->getJsonParams($inventory));

        /**
         * get inventory each product 
         * each warehouse
         * get current stock
         * 
         * create stock transfer information
         * calculate stock -> insert stock_transfer_items
         * 
         */
    }

    public function stockTransferDetails(Request $request, StockTransfer $stock_transfer)
    {
        return view('admin.inventory._show_stock_transfer', compact('stock_transfer'));
    }

    public function updateStatusStocktransfer(Request $request, StockTransfer $stock_transfer)
    {
        $request->validate([
            //'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' .  auth()->user()->merchantId()
        ]);

       $inventory = $this->inventory->updateStockTransferStatus($request, $stock_transfer);

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        return redirect()->back();
    }

}
