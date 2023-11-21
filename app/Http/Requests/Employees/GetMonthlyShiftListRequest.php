<?php

namespace App\Http\Requests\Employees;

use App\Filter\VacationRequests\MonthlyShiftFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetMonthlyShiftListRequest extends FormRequest
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
        $monthlyShiftFilter = new MonthlyShiftFilter();

        if ($this->filled('duration')) {
            $monthlyShiftFilter->setDuration($this->input('duration'));
        }

        if ($this->filled('order_by')) {
            $monthlyShiftFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $monthlyShiftFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $monthlyShiftFilter->setPerPage($this->input('per_page'));
        }
        return $monthlyShiftFilter;
    }
}
