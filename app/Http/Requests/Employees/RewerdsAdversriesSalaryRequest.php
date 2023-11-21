<?php

namespace App\Http\Requests\Employees;

use App\Statuses\AdversariesType;
use App\Statuses\RewardsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RewerdsAdversriesSalaryRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required',
            'rewards_type' => [Rule::in(RewardsType::$statuses), 'nullable'],
            'adversaries_type' => [Rule::in(AdversariesType::$statuses), 'nullable'],
            'rewards' => 'nullable|required_with:rewards_type',
            'adversaries' => 'nullable|required_with:adversaries_type',
        ];
    }
}
