<?php

namespace App\Http\Requests\Company;

use App\Filter\Company\CompanyFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetCompaniesListRequest extends FormRequest
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
        $companyFilter = new CompanyFilter();
        if ($this->filled('order_by')) {
            $companyFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $companyFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $companyFilter->setPerPage($this->input('per_page'));
        }
        return $companyFilter;
    }
}
