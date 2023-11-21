<?php

namespace App\Http\Requests\Alerts;

use App\Filter\Alerts\AlertFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetAlertsListRequest extends FormRequest
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
        $alertFilter = new AlertFilter();

        if ($this->filled('order_by')) {
            $alertFilter->setOrderBy($this->input('order_by'));
        } else {
            $alertFilter->setOrderBy('created_at');
        }

        if ($this->filled('order')) {
            $alertFilter->setOrder($this->input('order'));
        } else {
            $alertFilter->setOrder('DESC');
        }

        if ($this->filled('per_page')) {
            $alertFilter->setPerPage($this->input('per_page'));
        }
        return $alertFilter;
    }
}
