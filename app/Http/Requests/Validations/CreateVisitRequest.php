<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateVisitRequest extends Request
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
            'date' => 'required',
            'client_id' => 'required',
            'assignee_user_id' => 'required',
            'status' => 'required',
            'created_by' => 'required',
            'created_at' => 'required',
        ];
    }
}
