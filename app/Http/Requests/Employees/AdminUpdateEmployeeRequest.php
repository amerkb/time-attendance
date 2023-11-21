<?php

namespace App\Http\Requests\Employees;

use App\Statuses\MaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'material_status' => ['nullable', Rule::in(MaterialStatus::$statuses)],
            'housing_allowance' => 'nullable',
            'transportation_allowance' => 'nullable',
            'birthday_date' => 'nullable|date',
            'departement' => 'nullable|string',
            'position' => 'nullable|string',
            'address' => 'nullable|string',
            'guarantor' => 'nullable|string',
            'branch' => 'nullable|string',
            'skills' => 'nullable|string',
            'end_visa' => 'nullable|date',
            'end_employee_sponsorship' => 'nullable|date',
            'end_municipal_card' => 'nullable|date',
            'end_health_insurance' => 'nullable|date',
            'end_employee_residence' => 'nullable|date',
            'end_passport' => 'nullable|date',
            'id_photo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'biography' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'visa' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'municipal_card' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'employee_residence' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'health_insurance' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'passport' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'employee_sponsorship' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'phone' => 'nullable|unique:users,phone',
        ];
    }
}
