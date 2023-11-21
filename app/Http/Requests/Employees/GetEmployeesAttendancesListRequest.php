<?php

namespace App\Http\Requests\Employees;

use App\Filter\Attendance\AttendanceFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetEmployeesAttendancesListRequest extends FormRequest
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
        $attendanceFilter = new AttendanceFilter();


        if ($this->filled('order_by')) {
            $attendanceFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $attendanceFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $attendanceFilter->setPerPage($this->input('per_page'));
        }
        return $attendanceFilter;
    }
}
