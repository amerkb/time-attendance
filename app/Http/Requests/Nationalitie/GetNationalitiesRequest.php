<?php

namespace App\Http\Requests\Nationalitie;

use App\Filter\Nationalalities\NationalFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetNationalitiesRequest extends FormRequest
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
        $nationalFilter = new NationalFilter();


        if ($this->filled('order_by')) {
            $nationalFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $nationalFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $nationalFilter->setPerPage($this->input('per_page'));
        }
        return $nationalFilter;
    }
}
