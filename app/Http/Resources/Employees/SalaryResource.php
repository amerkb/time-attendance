<?php

namespace App\Http\Resources\Employees;

use App\Http\Resources\Admin\EmployeeResource;
use App\Services\Admin\AdminService;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\Translation\t;

class SalaryResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'net_salary' => AdminService::GenerateSalary($this->user, $this->salary),
            'basic_salary' => floatval($this->basic_salary),
            'update_salary' => $this->user->next_month_salary==0?false:true,
            'next_month_salary' => intval($this->user->next_month_salary),
            'rewards' => floatval($this->rewards),
            'rewards_type' => intval($this->rewards_type),
            'adversaries' => floatval($this->adversaries),
            'adversaries_type' => intval($this->adversaries_type),
            'housing_allowance' => floatval($this->housing_allowance),
            'transportation_allowance' => floatval($this->transportation_allowance),
            'date' => $this->date,
            'over_time' => AdminService::NumberOfOverTime($this->user),
            'discount_report' => AdminService::DiscountReport($this->user),
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
