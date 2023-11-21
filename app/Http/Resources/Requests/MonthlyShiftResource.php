<?php

namespace App\Http\Resources\Requests;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyShiftResource extends JsonResource
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
            "Id" => $this->id,
            "Date" => $this->date,
            "Status" => $this->status,
            // "Status" => $this->status == 1 ? "Present" : "Absent",
        ];
    }
}
