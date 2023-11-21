<?php

namespace App\Http\Requests\Holiday;

use App\Filter\Holiday\HolidayFilter;
use App\Models\Holiday;
use Illuminate\Foundation\Http\FormRequest;

class GetHolidaysRequest extends FormRequest
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
        $holidayFilter = new HolidayFilter();


        if ($this->filled('order_by')) {
            $holidayFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $holidayFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $holidayFilter->setPerPage($this->input('per_page'));
        }
        return $holidayFilter;
    }
}