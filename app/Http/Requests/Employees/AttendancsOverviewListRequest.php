<?php

namespace App\Http\Requests\Employees;

use App\Filter\Attendance\AttendanceOverviewFilter;
use Illuminate\Foundation\Http\FormRequest;

class AttendancsOverviewListRequest extends FormRequest
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
        $attendanceOverviewFilter = new AttendanceOverviewFilter();

        if ($this->filled('year')) {
            $attendanceOverviewFilter->setYear($this->input('year'));
        }


        if ($this->filled('order_by')) {
            $attendanceOverviewFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $attendanceOverviewFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $attendanceOverviewFilter->setPerPage($this->input('per_page'));
        }
        return $attendanceOverviewFilter;
    }
}
