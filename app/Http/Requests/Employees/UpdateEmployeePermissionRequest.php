<?php

namespace App\Http\Requests\Employees;

use App\Statuses\TimeSelect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeePermissionRequest extends FormRequest
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
            'entry_time' => ['nullable', Rule::in(TimeSelect::$statuses)],
            'leave_time' => ['nullable', Rule::in(TimeSelect::$statuses)]
        ];
    }
}
