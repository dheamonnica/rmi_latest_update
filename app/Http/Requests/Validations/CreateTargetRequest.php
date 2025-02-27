<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateTargetRequest extends Request
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
            'grand_total' => 'required',
        ];
    }
}
