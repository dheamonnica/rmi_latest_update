<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            'role_id' => $this->role_id,
            'name' => $this->name,
            'nice_name' => $this->nice_name,
            'dob' => $this->dob ? $this->dob : null,
            // 'dob' => $this->dob ? date('F j, Y', strtotime($this->dob)) : null,
            'sex' => $this->sex ? trans($this->sex) : null,
            'description' => $this->description,
            'active' => $this->active,
            'email' => $this->email,
            'phone' => $this->when(is_incevio_package_loaded('otp-login'), $this->phone),
            'member_since' => optional($this->created_at)->diffForHumans(),
            'avatar' => get_storage_file_url(optional($this->avatarImage)->path, 'small'),
            // 'last_visited_at' => $this->last_visited_at,
            // 'last_visited_from' => $this->last_visited_from,
            'api_token' => $this->when(isset($this->api_token), $this->api_token),
        ];
    }
}
