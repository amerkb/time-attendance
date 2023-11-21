<?php

namespace App\Http\Requests\Requests;

use App\Statuses\JustifyTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJustifyRequest extends FormRequest
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
            "reason" => "required|string",
            'date' => 'nullable|date',
            'start_date' => 'nullable|required_if:date,null|date',
            'end_date' => 'nullable|required_if:date,null|date',
            'justify_type' => ['required', Rule::in(JustifyTypes::$statuses)],
            "attachments" => "nullable|file|mimes:jpeg,png,jpg,pdf|max:5120",
        ];
    }
}
