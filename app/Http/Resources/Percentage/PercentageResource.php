<?php

namespace App\Http\Resources\Percentage;

use Illuminate\Http\Resources\Json\JsonResource;

class PercentageResource extends JsonResource
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
            'id' => $this->id,
            'rate_of_absents' => intval($this->rate_of_absents),
            'rate_of_committed_employees' => intval($this->rate_of_committed_employees),
            'rate_of_un_committed_employees' => intval($this->rate_of_un_committed_employees),
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}
