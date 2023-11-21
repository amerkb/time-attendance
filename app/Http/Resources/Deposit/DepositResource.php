<?php

namespace App\Http\Resources\Deposit;

use App\Statuses\DepositType;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
{

    public function toArray($request)
    {
        if ($this->type == DepositType::CAR) {
            return [
                "id" => $this->id,
                "type" => $this->type,
                "status" => $this->status,
                "extra_status" => $this->extra_status,
                "car_number" => $this->car_number,
                "car_model" => $this->car_model,
                "manufacturing_year" => $this->manufacturing_year,
                "mechanic_card_number" => $this->Mechanic_card_number,
                'car_image' => $this->car_image ? asset($this->car_image) : null,
                'reason_reject' => $this->reason_reject,
                'reason_clearance_reject' => $this->reason_clearance_reject,
                'deposit_request_date' => $this->deposit_request_date,
                'clearance_request_date' => $this->clearance_request_date,
                'user' => $this->whenLoaded('user', function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'image' => $this->user->image ? asset($this->user->image) : null,
                        'position' => $this->user->position,
                    ];
                }),
            ];
        } elseif ($this->type == DepositType::LAPTOP) {
            return [
                "id" => $this->id,
                "type" => $this->type,
                "status" => $this->status,
                "extra_status" => $this->extra_status,
                "laptop_type" => $this->laptop_type,
                "Serial_laptop_number" => $this->serial_laptop_number,
                "laptop_color" => $this->laptop_color,
                'laptop_image' => $this->laptop_image ? asset($this->laptop_image) : null,
                'reason_reject' => $this->reason_reject,
                'reason_clearance_reject' => $this->reason_clearance_reject,
                'deposit_request_date' => $this->deposit_request_date,
                'clearance_request_date' => $this->clearance_request_date,
                'user' => $this->whenLoaded('user', function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'image' => $this->user->image ? asset($this->user->image) : null,
                        'position' => $this->user->position,
                    ];
                }),
            ];
        } elseif ($this->type == DepositType::MOBILE) {
            return [
                "id" => $this->id,
                "type" => $this->type,
                "status" => $this->status,
                "extra_status" => $this->extra_status,
                "serial_mobile_number" => $this->serial_mobile_number,
                "mobile_color" => $this->mobile_color,
                "mobile_type" => $this->mobile_type,
                'mobile_sim' => $this->mobile_sim,
                'mobile_image' => $this->mobile_image ? asset($this->mobile_image) : null,
                'reason_reject' => $this->reason_reject,
                'reason_clearance_reject' => $this->reason_clearance_reject,
                'deposit_request_date' => $this->deposit_request_date,
                'clearance_request_date' => $this->clearance_request_date,
                'user' => $this->whenLoaded('user', function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'image' => $this->user->image ? asset($this->user->image) : null,
                        'position' => $this->user->position,
                    ];
                }),
            ];
        }
    }
}
