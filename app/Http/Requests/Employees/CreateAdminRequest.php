<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use App\Statuses\GenderStatus;
use App\Statuses\MaterialStatus;
use Illuminate\Validation\Rule;

class CreateAdminRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|max:255|unique:users,email',
            "password" => "required|min:8|max:24|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,24}$/",
            "work_email" => "nullable|email|max:255",
            'mobile' => 'required',
            'phone' => 'nullable',
            'company_id' => 'required',
            'serial_number' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    $companyId = $this->input('company_id');
                    $serialNumber = $this->input('serial_number');
                    $query->where('company_id', $companyId)
                        ->where('serial_number', $serialNumber);
                })
            ],
            'nationalitie_id' => 'required|exists:nationalities,id',
            'birthday_date' => 'required|date',
            'material_status' => ['nullable', Rule::in(MaterialStatus::$statuses)],
            'address' => 'nullable|string',
            "gender" => ["nullable", Rule::in(GenderStatus::$statuses)],
            'branch' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }
}
