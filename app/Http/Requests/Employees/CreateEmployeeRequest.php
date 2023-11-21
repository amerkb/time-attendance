<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use App\Statuses\GenderStatus;
use App\Statuses\MaterialStatus;
use App\Statuses\TimeSelect;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => [
                "required", 'email', 'max:2500', 'regex:/^[a-zA-Z0-9._%+-]{1,16}[^*]{0,}@[^*]+$/',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })
            ],
            "password" => "required|min:8|max:24|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,24}$/",
            "work_email" => "nullable|email|max:255|regex:/^[a-zA-Z0-9._%+-]{1,16}[^*]{0,}@[^*]+$/",
            'mobile' => 'required',
            'phone' => 'nullable',
            'nationalitie_id' => 'required|exists:nationalities,id',
            'birthday_date' => 'required|date',
            'material_status' => ['nullable', Rule::in(MaterialStatus::$statuses)],
            'departement' => 'nullable|string',
            'position' => 'nullable|string',
            'address' => 'nullable|string',
            'guarantor' => 'nullable|string',
            'branch' => 'nullable|string',
            'skills' => 'nullable|string',
            'serial_number' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })
            ],
            "gender" => ["nullable", Rule::in(GenderStatus::$statuses)],
            'start_job_contract' => 'required|date',
            'end_job_contract' => 'required|date',
            'end_visa' => 'nullable|date',
            'end_employee_sponsorship' => 'nullable|date',
            'end_municipal_card' => 'nullable|date',
            'end_health_insurance' => 'nullable|date',
            'end_employee_residence' => 'nullable|date',
            'leave_time' => ['nullable', Rule::in(TimeSelect::$statuses)],
            'entry_time' => ['nullable', Rule::in(TimeSelect::$statuses)],
            'end_passport' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'id_photo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'biography' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'visa' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'municipal_card' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'employee_residence' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'health_insurance' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'passport' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'employee_sponsorship' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'basic_salary' => 'required',
            'housing_allowance' => 'nullable',
            'hourly_annual' => 'nullable',
            'daily_annual' => 'nullable',
            'transportation_allowance' => 'nullable',
            'number_of_shifts' => 'nullable|integer|min:1',
            'shifts' => 'required_if:number_of_shifts,!=,null|array',
            'shifts.*.user_id' => 'required_if:shifts,null',
            'shifts.*.start_time' => 'required',
            'shifts.*.end_time' => 'required',
            'shifts.*.start_break_hour' => 'required',
            'shifts.*.end_break_hour' => 'required'
        ];
    }
}
