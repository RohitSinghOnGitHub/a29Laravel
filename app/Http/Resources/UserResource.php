<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            "user_id" => $this->id,
            "name" => $this->name,
            "mobile" => $this->mobile,
            "nickname" => $this->nickname,
            "email" => $this->email,
            "sponcer_id" => $this->sponcer_id
        ];
    }
}
