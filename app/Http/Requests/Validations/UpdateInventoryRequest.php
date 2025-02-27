<?php

namespace App\Http\Requests\Validations;

use App\Models\Inventory;
use App\Http\Requests\Request;

class UpdateInventoryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->is("api/vendor/*")) {
            $rules = [];

            return $rules;
        }

        if (!$this->input('key_features')) {
            $this->merge(['key_features' => null]);
        }

        if (!$this->input('linked_items')) {
            $this->merge(['linked_items' => null]);
        }

        $shop_id = $this->user()->merchantId(); //Get current user's shop_id
        $inventory = $this->route('inventory'); // Current model ID

        if ($this->is("api/vendor/*")) {
            $inventoryData = Inventory::find($inventory->id);
        } else {
            $inventoryData = Inventory::find($inventory);
        }

        if ($this->listing_type == 'auction') {
            $this->merge([
                'auctionable' => 1,
                'sale_price' => $this->sale_price ?? $this->base_price,
                'auction_status' => $inventoryData->auction_status != \Incevio\Package\Auction\Enums\AuctionStatusEnum::SUSPENDED && $this->auction_end > now() ? \Incevio\Package\Auction\Enums\AuctionStatusEnum::RUNNING : $inventoryData->auction_status,
            ]);
        }

        $min_price = $inventoryData->product->min_price;
        $max_price = $inventoryData->product->max_price;

        $rules = [
            'title' => 'required',
            'sale_price' => 'nullable|required_without:base_price|numeric|min:' . $min_price . ($max_price ? '|max:' . $max_price : ''),
            'base_price' => 'nullable|required_without:sale_price|numeric|min:' . $min_price . ($max_price ? '|max:' . $max_price : ''),
            'offer_price' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'auction_end' => 'nullable|date|after:available_from',
            'offer_start' => 'nullable|date|required_with:offer_price',
            'offer_end' => 'nullable|date|required_with:offer_price|after:offer_start',
            'image' => 'mimes:jpg,jpeg,png,gif',
            'shipping_weight' => 'required',
        ];

        //request check
        if ($this->is("api/vendor/*")) {
            $rules['sku'] = 'bail|required|composite_unique:inventories,sku,shop_id:' . $shop_id . ',' . $inventory->id;
            $rules['slug'] = 'bail|required|alpha_dash|unique:inventories,slug, ' . $inventory->id;
        } else {
            // $rules['sku'] = 'bail|required|composite_unique:inventories,sku,shop_id:' . $shop_id . ',' . $inventory;
            // $rules['slug'] = 'bail|required|alpha_dash|unique:inventories,slug, ' . $inventory;
        }

        if (is_incevio_package_loaded('pharmacy')) {
            $expiry_date_required = get_from_option_table('pharmacy_expiry_date_required', 1);

            $rules['expiry_date'] = (bool)$expiry_date_required ? 'required|date' : 'nullable|date';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required_with.required' => trans('validation.offer_start_required'),
            'offer_start.after_or_equal' => trans('validation.offer_start_after'),
            'required_with.required' => trans('validation.offer_end_required'),
            'offer_end.after' => trans('validation.offer_end_after'),
        ];
    }
}
