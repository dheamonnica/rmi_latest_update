<?php

namespace App\Http\Requests\Validations;

use App\Models\Role;
use App\Http\Requests\Request;

class CreateMerchantRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        incevioAutoloadHelpers(getMysqliConnection());
        return Request::user()->isFromPlatform();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Request::merge(['role_id' => Role::MERCHANT]); //Set role_id

        $rules =  [
            'name' => 'required|max:255',
            // 'legal_name' => 'required',
            'slug' => 'required|alpha_dash|max:255|unique:shops',
            // 'shop_name' => 'required|string|max:255|unique:shops,name',
            'email' =>  'required|email|max:255|unique:users',
            'external_url' => 'nullable|url',
            'password' =>  'required|min:6',
            'active' => 'required',
            'image' => 'max:' . config('system_settings.max_img_size_limit_kb') . '|mimes:jpg,jpeg,png,gif',
        ];

        if (is_incevio_package_loaded('otp-login')) {
            $rules['phone'] = 'required|string|unique:users';
        }

        return $rules;
    }
}
