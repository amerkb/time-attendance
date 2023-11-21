<?php

namespace App\Http\Requests\Requests;

use App\Filter\JustifyRequests\JustifyRequestsFilter;
use App\Filter\VacationRequests\VacationRequestFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetJustifyRequestListRequest extends FormRequest
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
        $justifyRequestsFilter = new JustifyRequestsFilter();

        if ($this->filled('order_by')) {
            $justifyRequestsFilter->setOrderBy($this->input('order_by'));
        } else {
            $justifyRequestsFilter->setOrderBy('created_at');
        }

        if ($this->filled('order')) {
            $justifyRequestsFilter->setOrder($this->input('order'));
        } else {
            $justifyRequestsFilter->setOrder('DESC');
        }

        if ($this->filled('per_page')) {
            $justifyRequestsFilter->setPerPage($this->input('per_page'));
        }
        return $justifyRequestsFilter;
    }
}
