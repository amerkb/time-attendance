<?php

namespace App\Http\Requests\Employees;

use App\Filter\Employees\LeaveCalendarFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetLeaveCalendarListRequest extends FormRequest
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
        $leaveCalendarFilter = new LeaveCalendarFilter();

        if ($this->filled('date')) {
            $leaveCalendarFilter->setDate($this->input('date'));
        }

        if ($this->filled('day')) {
            $leaveCalendarFilter->setDay($this->input('day'));
        }

        if ($this->filled('order_by')) {
            $leaveCalendarFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $leaveCalendarFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $leaveCalendarFilter->setPerPage($this->input('per_page'));
        }
        return $leaveCalendarFilter;
    }
}
