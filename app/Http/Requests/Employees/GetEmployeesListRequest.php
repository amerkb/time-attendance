<?php

namespace App\Http\Requests\Employees;

use App\Filter\Employees\EmployeeFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetEmployeesListRequest extends FormRequest
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
        $employeeFilter = new EmployeeFilter();

        if ($this->input('name')) {
            $employeeFilter->setName($this->input('name'));
        }

        if ($this->filled('order_by')) {
            $employeeFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $employeeFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $employeeFilter->setPerPage($this->input('per_page'));
        }
        return $employeeFilter;
    }
}
