<?php

namespace App\Http\Requests\Employees;

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
            'user_id' => 'required',
            'number_of_month' => ['nullable', Rule::in(TerminateTime::$statuses), 'required_without:new_date'],
            'new_date' => ['nullable', 'date_format:Y-m-d', 'required_without_all:number_of_month'],
        ];
    }
}
