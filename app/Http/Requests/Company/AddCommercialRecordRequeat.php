<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class AddCommercialRecordRequeat extends FormRequest
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
            'company_id' => 'required|exists:companies,id',
            'start_commercial_record' => 'required|date',
            'end_commercial_record' => 'required|date',
            'commercial_record' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ];
    }
}
