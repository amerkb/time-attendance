<?php

namespace App\Http\Requests\Contract;

use App\Statuses\TerminateTime;
use App\Statuses\TerminateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TerminateContractRequest extends FormRequest
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


    public function rules()
    {
        return [
            "user_id" => "required|exists:users,id",
            'terminate_type' => ['required', Rule::in(TerminateType::$statuses)],

            'contract_termination_period' => [
                Rule::requiredIf(function () {
                    return request()->input('terminate_type') == TerminateType::TEMPORARY;
                }), 'nullable'
            ],
            "contract_termination_reason" => [
                Rule::requiredIf(function () {
                    return request()->input('terminate_type') == TerminateType::TEMPORARY;
                }), 'nullable'
            ],
        ];
    }
}
