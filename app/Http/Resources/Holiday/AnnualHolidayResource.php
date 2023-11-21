<?php

namespace App\Http\Resources\Holiday;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnualHolidayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'holiday_name' => $this->holiday_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}
