<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankCardResource extends JsonResource
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
            "Card_id" => $this->id,
            "Name"=>$this->Name,
            "IFSC" => $this->IFSC,
            "Bank_name" => $this->Bank_Name,
            "Account_No" => $this->Account_No,
            "State" => $this->State,
            "City" => $this->City,
            "Address" => $this->Address,
            "UPI" => $this->UPI,
            "Email" => $this->Email,
        
        ];
    }
}
