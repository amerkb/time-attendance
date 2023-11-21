<?php

namespace App\Http\Resources\Shifts;

use Illuminate\Http\Resources\Json\JsonResource;

class ShiftRersource extends JsonResource
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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'start_break_hour' => $this->start_break_hour,
            'end_break_hour' => $this->end_break_hour,
        ];
    }
}
