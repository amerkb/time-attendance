<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;

class CheckInAttendanceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'check_in' => 'required',


            'mac_address' => 'nullable|array',
            'mac_address.*' => 'nullable',

            'longitude' => 'required_without_all:mac_address,latitude|nullable',
            'latitude' => 'required_without_all:mac_address,longitude|nullable',
        ];
    }
}
