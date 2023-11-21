<?php

namespace App\Http\Requests\Employees;

use App\Filter\Salary\SalaryFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetEmployeesSalariesListRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            //
        ];
    }

    public function generateFilter()
    {
        $salaryFilter = new SalaryFilter();


        if ($this->filled('order_by')) {
            $salaryFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $salaryFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $salaryFilter->setPerPage($this->input('per_page'));
        }
        return $salaryFilter;
    }
}
