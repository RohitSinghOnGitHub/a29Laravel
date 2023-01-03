<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "Name" => $this->name,
            "Amount"=>$this->Amount,
            "UPI" => $this->upi,
            "UTR" => $this->UTR,
            "Email_id" => $this->Email_id,
            "Image_Path" => $this->image_path,
            "Status" => $this->status
        
        ];
    }
}
