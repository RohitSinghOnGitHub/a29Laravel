<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "Period" => $this->Period,
            "Contract_Money" => $this->Contract_Money,
            "Contract_Count" => $this->Contract_Count,
            "Delivery" => $this->Delivery,
            "Fee" => $this->Fee,
            "Select" => $this->Select,
            "Status" => $this->Status,
            "Amount" => $this->Amount,
            "Win amount" => $this->win_amount,
            "Category" => $this->category,
        ];
    }
}
