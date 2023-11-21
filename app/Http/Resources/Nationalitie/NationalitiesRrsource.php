<?php

namespace App\Http\Resources\Nationalitie;

use Illuminate\Http\Resources\Json\JsonResource;

class NationalitiesRrsource extends JsonResource
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
            'name' => $this->name,
            'name_arabic' => $this->name_arabic
        ];
    }
}
