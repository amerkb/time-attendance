<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'start_employee_contract_date' => $this->start_contract_date,
            'end_employee_contract_date' => $this->end_contract_date,
            'contract_termination_date' => $this->contract_termination_date,
            'contract_termination_period' => $this->contract_termination_period,
            'contract_termination_reason' => $this->contract_termination_reason,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'image' => $this->user->image ? asset($this->user->image) : null,
                    'position' => $this->user->position,
                    'basic_salary' => floatVal($this->user->basic_salary),
                ];
            }),
        ];
    }
}
