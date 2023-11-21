<?php

namespace App\Http\Requests\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRetirementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'reason' => 'required',
            "attachments" => "nullable|file|mimes:jpeg,png,jpg,pdf|max:5120",
        ];
    }
}
