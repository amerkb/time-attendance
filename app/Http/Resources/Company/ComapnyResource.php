<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\Addresess\AddressResource;
use App\Http\Resources\Admin\EmployeeResource;
use App\Http\Resources\Admin\EmployeeSuperAdminResource;
use App\Http\Resources\Location\LocationResource;
use App\Services\Company\CompanyService;
use Illuminate\Http\Resources\Json\JsonResource;

class ComapnyResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'commercial_record' => $this->commercial_record ? asset($this->commercial_record) : null,
            'start_commercial_record' => $this->start_commercial_record,
            'end_commercial_record' => $this->end_commercial_record,
            'check_type' => $this->check_type,
            'number_of_employees' => CompanyService::numberOfEmployee($this->id),
            'number_of_dismissed_employees' => CompanyService::numberOfDismissedEmployee($this->id),
            'location' => LocationResource::make($this->whenLoaded('locations')),
            'addresess' => AddressResource::collection($this->whenLoaded('addresess')),
            'employees' => EmployeeSuperAdminResource::collection($this->whenLoaded('employees')),
            'dismissedEmployees' => EmployeeSuperAdminResource::collection($this->whenLoaded('dismissedEmployees')),
            'admin' => $this->whenLoaded('admin', function () {
                return [
                    'id' => $this->admin->id,
                    'name' => $this->admin->name,
                ];
            }),
        ];
    }
}
