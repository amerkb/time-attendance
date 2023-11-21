<?php

namespace App\Http\Requests\Alerts;

use App\Statuses\AlertTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrateAlertRequest extends FormRequest
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
            'content' => 'required|string|max:100',
            'email' => 'required|exists:users,email|email',
            'type' => ['required', Rule::in(AlertTypes::$statuses)],
        ];
    }
}
