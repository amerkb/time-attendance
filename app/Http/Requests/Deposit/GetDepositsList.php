<?php

namespace App\Http\Requests\Deposit;

use App\Filter\Deposit\DepositFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetDepositsList extends FormRequest
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
        $depositFilte = new DepositFilter();

        if ($this->filled('status')) {
            $depositFilte->setStatus($this->input('status'));
        }
        if ($this->filled('order_by')) {
            $depositFilte->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $depositFilte->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $depositFilte->setPerPage($this->input('per_page'));
        }
        return $depositFilte;
    }
}
