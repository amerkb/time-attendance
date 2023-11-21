<?php

namespace App\Http\Requests\Requests;

use App\Statuses\KinShipType;
use App\Statuses\PatientType;
use App\Statuses\PaymentType;
use App\Statuses\VacationRequestTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateVacationRequest extends FormRequest
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
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'vacation_type' => ['required', Rule::in(VacationRequestTypes::$statuses)],
            'start_time' => 'required_if:vacation_type,1',
            'end_time' => 'required_if:vacation_type,1',
            'payment_type' => ['required', Rule::in(PaymentType::$statuses)],
            'person' => ['required_if:vacation_type,4', Rule::in(PatientType::$statuses1)],
            'dead_person' => ['required_if:vacation_type,3', Rule::in(PatientType::$statuses2)],
            'degree_of_kinship' => ['required_if:vacation_type,3', Rule::in(KinShipType::$statuses)],
            "attachments" => "nullable|file|mimes:jpeg,png,jpg,pdf|max:5120",
        ];
    }
}