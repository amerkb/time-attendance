<?php

namespace App\Http\Requests\Company;

use App\Statuses\CheckType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CreateComapnyRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            "name" => "required|string",
            "email" => "required|email",
            'type' => ['required', Rule::in(CheckType::$statuses)],
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'radius' => 'nullable',
            'mac_address' => 'nullable|array',
            'mac_address.*' => 'nullable',
        ];
    }
}
