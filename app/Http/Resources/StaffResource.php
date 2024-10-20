<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StaffResource extends JsonResource
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
      'name' => $this->getName(),
      'email' => $this->email,
      'member_since' => optional($this->created_at)->diffForHumans(),
      'active' => $this->active,
      'avatar' => get_storage_file_url(optional($this->avatarImage)->path, 'small'),
      $this->mergeWhen($request->is('api/vendor/*'), [
        'role_id' => $this->role->id,
        'nice_name' => $this->nice_name,
        'description' => $this->description,
      ])
    ];
  }
}
