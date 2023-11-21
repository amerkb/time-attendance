<?php

namespace App\Http\Requests\Requests;

use App\Filter\VacationRequests\VacationRequestFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetVacationRequestListRequest extends FormRequest
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
        return [];
    }
    public function generateFilter()
    {
        $vacationRequestFilter = new VacationRequestFilter();

        if ($this->filled('duration')) {
            $vacationRequestFilter->setDuration($this->input('duration'));
        }

        if ($this->filled('order_by')) {
            $vacationRequestFilter->setOrderBy($this->input('order_by'));
        } else {
            $vacationRequestFilter->setOrderBy('created_at');
        }

        if ($this->filled('order')) {
            $vacationRequestFilter->setOrder($this->input('order'));
        } else {
            $vacationRequestFilter->setOrder('DESC');
        }

        if ($this->filled('per_page')) {
            $vacationRequestFilter->setPerPage($this->input('per_page'));
        }
        return $vacationRequestFilter;
    }
}
