<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            "company_id" => "required|exists:companies,id",
            "name" => "nullable|string",
            "email" => "nullable|email",
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'radius' => 'nullable',
            'mac_address' => 'nullable|array',
//            'mac_address.*' => 'required_if:mac_address,*|string',
            'address_id' => 'nullable|exists:addresses,id',
        ];
    }
}
