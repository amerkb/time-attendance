<?php

namespace App\Http\Resources\Admin;

use App\Services\Admin\AdminService;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeSuperAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user_id = $this->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'work_email' => $this->work_email,
            'status' => $this->status,
            'type' => $this->type,
            'company_id' => $this->company_id,
            'gender' => intval($this->gender),
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'departement' => $this->departement,
            'address' => $this->address,
            'position' => $this->position,
            'skills' => $this->skills,
            'serial_number' => $this->serial_number,
            'birthday_date' => $this->birthday_date,
            'material_status' => intval($this->material_status),
            'guarantor' => $this->guarantor,
            'branch' => $this->branch,
            'image' => $this->image ? asset($this->image) : null,
            'number_of_working_hours' => AdminService::CalculateNumberOfWorkingHours($user_id),
        ];
    }
}
