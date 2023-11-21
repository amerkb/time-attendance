<?php

namespace App\Http\Resources\Holiday;

use App\Statuses\HolidayTypes;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayRrsource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->type == HolidayTypes::ANNUL) {
            return [
                'id' => $this->id,
                'type' => $this->type,
                'holiday_name' => $this->holiday_name,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ];
        } else {
            return [
                'id' => $this->id,
                'type' => $this->type,
                'day' => $this->day,
                'day_name' => $this->day_name
            ];
        }
    }
}
