<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;


class DashboardDataResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'all_employees_count' => $this->all_employees_count,
            'attendance_rate' => $this->attendance_rate,
            'active_employees_count' => $this->active_employees_count,
            'on_vacation_employees_count' => $this->on_vacation_employees_count,
            'active_employees_percentage' => $this->active_employees_percentage,
            'on_vacation_employees_percentage' => $this->on_vacation_employees_percentage,
            'male_employees_percentage' => $this->male_employees,
            'female_employees_percentage' => $this->female_employees,
            'male_employees_count' => $this->male_employees_count,
            'female_employees_count' => $this->female_employees_count,
            'nationalities_rate' => $this->nationalities_rate,
            'contract_expiration_percentage' => $this->contract_expiration_percentage,
            'contract_expiration' => $this->contract_expiration,
            'expired_passports_percentage' => $this->expired_passports_percentage,
            'expired_passports' => $this->expired_passports,

        ];
    }
}
