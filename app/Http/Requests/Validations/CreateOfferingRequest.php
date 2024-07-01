<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateOfferingRequest extends Request
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
        return [
            'product_id' => 'required',
            'small_quantity_price' => 'required',
            'medium_quantity_price' => 'required',
            'large_quantity_price' => 'required',
            'created_by' => 'required',
            'created_at' => 'required',
        ];
    }
}
