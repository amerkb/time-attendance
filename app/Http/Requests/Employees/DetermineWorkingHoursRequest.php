<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;

class DetermineWorkingHoursRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'hourly_annual' => 'sometimes',
            'daily_annual' => 'sometimes'
        ];
    }
}
