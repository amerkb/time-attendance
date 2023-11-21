<?php

namespace App\Http\Requests\Requests;

use App\Filter\JustifyRequests\JustifyRequestsFilter;
use App\Filter\VacationRequests\RequestFilter;
use App\Filter\VacationRequests\VacationRequestFilter;
use Illuminate\Foundation\Http\FormRequest;


class GetRequestListRequest extends FormRequest
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
        $requestFilter = new RequestFilter();


        if ($this->filled('type')) {
            $requestFilter->setRequestType($this->input('type'));
        }
        if ($this->filled('order_by')) {
            $requestFilter->setOrderBy($this->input('order_by'));
        } else {
            $requestFilter->setOrderBy('created_at');
        }

        if ($this->filled('order')) {
            $requestFilter->setOrder($this->input('order'));
        } else {
            $requestFilter->setOrder('DESC');
        }

        if ($this->filled('per_page')) {
            $requestFilter->setPerPage($this->input('per_page'));
        }
        return $requestFilter;
    }
}
