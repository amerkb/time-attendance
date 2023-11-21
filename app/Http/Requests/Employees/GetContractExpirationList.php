<?php

namespace App\Http\Requests\Employees;

use App\Filter\Contract\ContractFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetContractExpirationList extends FormRequest
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
            //
        ];
    }

    public function generateFilter()
    {
        $contractFilter = new ContractFilter();


        if ($this->filled('order_by')) {
            $contractFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $contractFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $contractFilter->setPerPage($this->input('per_page'));
        }
        return $contractFilter;
    }
}
