<?php

namespace App\Repositories\Inventory;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EloquentInventory extends EloquentRepository implements BaseRepository, InventoryRepository
{
    protected $model;

    public function __construct(Inventory $inventory)
    {
        $this->model = $inventory;
    }

    public function all($status = null)
    {
        $inventory = $this->model->with('product', 'image');

        switch ($status) {
            case 'active':
                $inventory = $inventory->active();
                break;

            case 'inactive':
                $inventory = $inventory->inActive();
                break;

            case 'outOfStock':
                $inventory = $inventory->stockOut();
                break;
        }

        if (!Auth::user()->isFromPlatform()) {
            return $inventory->mine()->get();
        }

        return $inventory->get();
    }

    public function trashOnly()
    {
        if (!Auth::user()->isFromPlatform()) {
            return $this->model->mine()->onlyTrashed()->with('product', 'image')->get();
        }

        return $this->model->onlyTrashed()->with('product', 'image')->get();
    }

    public function checkInventoryExist($productId)
    {
        return $this->model->mine()->where('product_id', $productId)->first();
    }

    public function store(Request $request)
    {
        //check SKU if SKU exist && expired date new, so then update the existig

        $findInventory = $this->model->where(['sku' => $request->sku, 'expired_date' => $request->expired_date])->first();

        if ($findInventory) {
            return $this->update($request, $findInventory->id);
        }
        
        $inventory = parent::store($request);

        $this->setAttributes($inventory, $request->input('variants'));

        if (is_incevio_package_loaded('packaging') && $request->input('packaging_list')) {
            $inventory->packagings()->sync($request->input('packaging_list'));
        }

        if ($request->input('tag_list')) {
            $inventory->syncTags($inventory, $request->input('tag_list'));
        }

        if ($request->hasFile('image')) {
            $inventory->saveImage($request->file('image'));
        }

        if ($request->hasFile('digital_file')) {
            $inventory->saveAttachments($request->file('digital_file'));
        }

        return $inventory;
    }

    public function storeWithVariant(Request $request)
    {
        $product = json_decode($request->input('product'));

        // Common informations
        $commonInfo = [
            'user_id' => $request->user()->id, // Set user_id
            'shop_id' => $request->user()->merchantId(), // Set shop_id
            'title' => $request->has('title') ? $request->input('title') : $product->name,
            'product_id' => $product->id,
            'brand' => $product->brand,
            'warehouse_id' => $request->input('warehouse_id'),
            'supplier_id' => $request->input('supplier_id'),
            'shipping_width' => $request->input('shipping_width'),
            'shipping_height' => $request->input('shipping_height'),
            'shipping_depth' => $request->input('shipping_depth'),
            'shipping_weight' => $request->input('shipping_weight'),
            'available_from' => $request->input('available_from'),
            'active' => $request->input('active'),
            'tax_id' => $request->input('tax_id'),
            'min_order_quantity' => $request->input('min_order_quantity'),
            'alert_quantity' => $request->input('alert_quantity'),
            'description' => $request->input('description'),
            'condition_note' => $request->input('condition_note'),
            'key_features' => $request->input('key_features'),
            'linked_items' => $request->input('linked_items'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
        ];

        // Arrays
        $skus = $request->input('sku');
        $conditions = $request->input('condition');
        $stock_quantities = $request->input('stock_quantity');
        $purchase_prices = $request->input('purchase_price');
        $sale_prices = $request->input('sale_price');
        $offer_prices = $request->input('offer_price');
        $images = $request->file('image');

        // Relations
        $tag_lists = $request->input('tag_list');
        $variants = $request->input('variants');
        if (is_incevio_package_loaded('packaging')) {
            $packaging_lists = $request->input('packaging_list');
        }

        $isFirst = true;
        $parent_id = null;

        //Preparing data and insert records.
        $dynamicInfo = [];
        foreach ($skus as $key => $sku) {
            $dynamicInfo = [
                'sku' => $skus[$key],
                'stock_quantity' => $stock_quantities[$key],
                'purchase_price' => $purchase_prices[$key],
                'sale_price' => $sale_prices[$key],
                'offer_price' => ($offer_prices[$key]) ? $offer_prices[$key] : null,
                'offer_start' => ($offer_prices[$key]) ? $request->input('offer_start') : null,
                'offer_end' => ($offer_prices[$key]) ? $request->input('offer_end') : null,
                'slug' => Str::slug($request->input('slug') . ' ' . $sku, '-'),
                'parent_id' => $parent_id,
            ];

            if (config('system_settings.show_item_conditions')) {
                $dynamicInfo['condition'] = $conditions[$key];
            }

            // Merge the common info and dynamic info to data array
            $data = array_merge($dynamicInfo, $commonInfo);

            // Insert the record
            $inventory = Inventory::create($data);

            if ($isFirst) {
                $parent_id = $inventory->id;
                $isFirst = false;
            }

            // Sync Attributes
            if ($variants[$key]) {
                $this->setAttributes($inventory, $variants[$key]);
            }

            // Sync packaging
            if (is_incevio_package_loaded('packaging') && $packaging_lists) {
                $inventory->packagings()->sync($packaging_lists);
            }

            // Sync tags
            if ($tag_lists) {
                $inventory->syncTags($inventory, $tag_lists);
            }

            // Save Images
            if (isset($images[$key])) {
                $inventory->saveImage($images[$key]);
            }
        }

        return true;
    }

    public function updateQtt(Request $request, $id)
    {
        $inventory = parent::find($id);

        $inventory->stock_quantity = $request->input('stock_quantity');

        return $inventory->save();
    }

    public function update(Request $request, $id)
    {
        $inventory = parent::update($request, $id);

        $this->setAttributes($inventory, $request->input('variants'));

        if (is_incevio_package_loaded('packaging')) {
            $inventory->packagings()->sync($request->input('packaging_list', []));
        }

        $inventory->syncTags($inventory, $request->input('tag_list', []));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1)) {
            $inventory->deleteImage();
        }

        if ($request->hasFile('image')) {
            $inventory->saveImage($request->file('image'));
        }

        return $inventory;
    }

    public function destroy($inventory)
    {
        if (!$inventory instanceof Inventory) {
            $inventory = parent::findTrash($inventory);
        }

        $inventory->detachTags($inventory->id, 'inventory');

        $inventory->flushImages();

        $inventory->flushAttachments();


        return $inventory->forceDelete();
    }

    public function massDestroy($ids)
    {
        $inventories = $this->model->withTrashed()->whereIn('id', $ids)->get();

        foreach ($inventories as $inventory) {
            $inventory->detachTags($inventory->id, 'inventory');
            $inventory->flushImages();
            $inventory->flushAttachments();
        }

        return parent::massDestroy($ids);
    }

    public function emptyTrash()
    {
        $inventories = $this->model->onlyTrashed()->get();

        foreach ($inventories as $inventory) {
            $inventory->detachTags($inventory->id, 'inventory');
            $inventory->flushImages();
            $inventory->flushAttachments();
        }

        return parent::emptyTrash();
    }

    public function findProduct($id)
    {
        return Product::findOrFail($id);
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

    public function getAttributeList(array $variants)
    {
        return Attribute::find($variants)->pluck('name', 'id');
    }

    /**
     * Check the list of attribute values and add new if need
     * @param  [type] $attribute
     * @param  array  $values
     * @return array
     */
    public function confirmAttributes($attributeWithValues)
    {
        $results = [];

        foreach ($attributeWithValues as $attribute => $values) {
            foreach ($values as $value) {
                $oldValueId = AttributeValue::find($value);

                $oldValueName = AttributeValue::where('value', $value)->where('attribute_id', $attribute)->first();

                if ($oldValueId || $oldValueName) {
                    $results[$attribute][($oldValueId) ? $oldValueId->id : $oldValueName->id] = ($oldValueId) ? $oldValueId->value : $oldValueName->value;
                } else {
                    // if the value not numeric thats meaninig that its new value and we need to create it
                    $newID = AttributeValue::insertGetId(['attribute_id' => $attribute, 'value' => $value]);

                    $newAttrValue = AttributeValue::find($newID);

                    $results[$attribute][$newAttrValue->id] = $newAttrValue->value;
                }
            }
        }

        return $results;
    }

    public function storeStockTransfer(Request $request)
    {
        $product = $request->input('product');
        $transfer_date = date('Y-m-d');

        $data = [
            'movement_number' => $request->input('movement_number'),
            'shop_depature_id' => (int) $request->input('shop_depature_id'),
            'shop_arrival_id' => (int) $request->input('shop_arrival_id'),
            'status' => Inventory::STATUS_STOCK_TRANSFER_PACKING,
            'transfer_date' => date('Y-m-d'),
            'packed_time' => date('Y-m-d h:i:s'),
            'admin_note' => $request->input('admin_note'),
            'transfer_type' => $request->input('transfer_type'),
            'transfer_by' => Auth::user()->id,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];

        $stock_transfer = StockTransfer::create($data);
        $stock_tansfer_items = [];

        foreach($product as $item) {
            //cek stok jika tidak ada di handle bagaimana 
            $dest_stock = Inventory::where(['product_id' => (int) $item['product_id'], 'shop_id' => (int) $request->input('shop_arrival_id'), 'active' => 1])->orderBy('expired_date', 'DESC')->first();

            if(!$dest_stock){
                //get item inventory from , create to;(duplicate change shop_id)
                $currect_inventory = Inventory::where(['id' => $item['inventory_id']])->first();

                //force create new items
                $dest_stock = Inventory::create([
                    'parent_id' => $currect_inventory->parent_id,
                    'shop_id'  => (int) $request->input('shop_arrival_id'),//new shop id
                    'title'  => $currect_inventory->title,
                    'warehouse_id'  => $currect_inventory->warehouse_id,
                    'product_id'  => $currect_inventory->product_id,
                    'brand'  => $currect_inventory->brand,
                    'supplier_id'  => $currect_inventory->supplier_id,
                    'sku'  => $currect_inventory->sku,
                    'condition'  => $currect_inventory->condition,
                    'condition_note'  => $currect_inventory->condition_note,
                    'description'  => $currect_inventory->description,
                    'download_limit'  => $currect_inventory->download_limit,
                    'key_features'  => $currect_inventory->key_features,
                    'user_id'  => $currect_inventory->user_id,
                    'purchase_price'  => $currect_inventory->purchase_price,
                    'sale_price'  => $currect_inventory->sale_price,
                    'offer_price'  => $currect_inventory->offer_price,
                    // 'offer_start'  => $currect_inventory->offer_start,
                    // 'offer_end'  => $currect_inventory->offer_end,
                    'shipping_weight'  => $currect_inventory->shipping_weight,
                    'length'  => $currect_inventory->length,
                    'width'  => $currect_inventory->width,
                    'height'  => $currect_inventory->height,
                    'free_shipping'  => $currect_inventory->free_shipping,
                    'stuff_pick'  => $currect_inventory->stuff_pick,
                    // 'available_from'  => $currect_inventory->available_from,
                    'expiry_date'  => $currect_inventory->expiry_date,
                    'min_order_quantity'  => $currect_inventory->min_order_quantity,
                    'linked_items'  => $currect_inventory->linked_items,
                    'slug'  => $currect_inventory->slug,
                    'meta_title'  => $currect_inventory->meta_title,
                    'meta_description'  => $currect_inventory->meta_description,
                    'active'  => $currect_inventory->active,
                    // 'auctionable'  => $currect_inventory->auctionable,
                    // 'auction_status'  => $currect_inventory->auction_status,
                    // 'base_price'  => $currect_inventory->base_price,
                    // 'auction_end'  => $currect_inventory->auction_end,
                    // 'bid_accept_action'  => $currect_inventory->bid_accept_action,
                    'expired_date'  => $currect_inventory->expired_date,
                    'uom'  => $currect_inventory->uom,
                ]);
            }

            $transferStock = [
                'stock_transfer_id' => $stock_transfer->id,
                'product_id' => (int) $item['product_id'],
                'from_inventory_id' => (int) $item['inventory_id'],
                'to_inventory_id' => $dest_stock->id,
                'before_depature_stock' => (int) $item['stock_quantity'],
                'after_depature_stock' => (int) $item['stock_quantity'] - (int) $item['transfer_quantity'],
                'before_arrival_stock' => (int) $dest_stock->stock_quantity,
                'after_arrival_stock' => (int) $dest_stock->stock_quantity + (int) $item['transfer_quantity'],
                'transfer_qty' => (int) $item['transfer_quantity'],
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];

            $stock_tansfer_items = StockTransferItem::create($transferStock);
        }
        
        return [
            'stock_trasfer' => $stock_transfer,
            'stock_transfer_items' => $stock_tansfer_items,
        ];
    }

    public function updateStockTransferStatus(Request $request, $stockTransfer)
    {
        $userId = Auth::user()->id;
        /**
         * get data stock transfer 
         * 
         * get items
         */
        if((int) $request->input('status') == Inventory::STATUS_STOCK_TRANSFER_DELIVERED){
            $data = [
                'status' => (int) $request->status,
                'delivered_time' => date('Y-m-d h:i:s'),
                'delivered_by' => $userId,
                'updated_by' => $userId,
            ];
        } else if((int) $request->input('status') == Inventory::STATUS_STOCK_TRANSFER_RECEIVED_BY) {
            $data = [
                'status' => (int) $request->status,
                'received_time' => date('Y-m-d h:i:s'),
                'received_by' => $userId,
                'updated_by' => $userId,
            ];
        } else if((int) $request->input('status') == Inventory::STATUS_STOCK_TRANSFER_SEND_BY_WAREHOUSE) {
            $data = [
                'status' => (int) $request->status,
                'send_by_warehouse_time' => date('Y-m-d h:i:s'),
                'send_by_warehouse' => $userId,
                'updated_by' => $userId,
            ];
        } else if((int) $request->input('status') == Inventory::STATUS_STOCK_TRANSFER_ON_DELIVERY) {
            $data = [
                'status' => (int) $request->status,
                'on_delivery_time' => date('Y-m-d h:i:s'),
                'on_delivery_by' => $userId,
                'updated_by' => $userId,
            ];
        } else if((int) $request->input('status') == Inventory::STATUS_STOCK_TRANSFER_APPROVED) {
            $data = [
                'status' => (int) $request->status,
                'approved_by_time' => date('Y-m-d h:i:s'),
                'approved_by' => $userId,
                'updated_by' => $userId,
            ];
        }else {
            $data = [
                'status' => (int) $request->status,
                'updated_by' => $userId,
            ];
        }

        // $data['status'] = (int) $request->input('status');
        $stock_transfer = $stockTransfer->update($data);


        $stock_transfer_item = StockTransferItem::where(['stock_transfer_id' => $stockTransfer->id])->get();

        foreach($stock_transfer_item as $item){
            // $item->update(['updated_by' => $userId]);
            $updateItem = StockTransferItem::find($item->id)->update(['updated_by', $userId]);

            if((int) $request->input('status') == Inventory::STATUS_STOCK_TRANSFER_APPROVED){
                $updateFromInventory = Inventory::where('id', $item->from_inventory_id)->first();
                $updateFromInventory->update(['stock_quantity' => $updateFromInventory->stock_quantity - $item->transfer_qty, 'updated_by' => $userId]);
                $updateToInventory = Inventory::where('id', $item->to_inventory_id)->first();
                $updateToInventory->update(['stock_quantity' => $updateToInventory->stock_quantity - $item->transfer_qty, 'updated_by' => $userId]);

            }
        }
        
        return $stock_transfer;
    }
}
