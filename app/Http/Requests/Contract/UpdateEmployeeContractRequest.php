<?php

namespace App\Http\Requests\Contract;

use App\Statuses\TerminateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "user_id" => "required|exists:users,id",
            'contract_termination_period' => ['required', Rule::in(TerminateTime::$statuses)],
            "contract_termination_reason" => "required"
        ];
    }
}
