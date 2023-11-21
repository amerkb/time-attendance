<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeShift extends FormRequest
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
            'shift_id' => 'required|exists:shifts,id',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'start_break_hour' => 'nullable',
            'end_break_hour' => 'nullable'
        ];
    }
}
