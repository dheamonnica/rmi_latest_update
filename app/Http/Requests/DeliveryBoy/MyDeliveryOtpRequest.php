<?php

namespace App\Http\Requests\DeliveryBoy;

use Illuminate\Foundation\Http\FormRequest;

class MyDeliveryOtpRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return $this->route('order')->delivery_boy_id &&
      $this->route('order')->delivery_boy_id == $this->user()->id;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'otp' => 'required|string|size:6|unique:orders'
    ];
  }
}
