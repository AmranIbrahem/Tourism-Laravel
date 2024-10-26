<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class addTripRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'AreaNames' => 'required|array',
            'AreaNames.*' => 'string|exists:areas,AreaName',
            'NumberOfPeople' => 'required|integer',
            'Cost' => 'required|numeric',
            'TripDetails' => 'required|string',
            'TripHistory' => 'required|string',
            'RegistrationStartDate' => 'required|date|before:RegistrationEndDate',
            'RegistrationEndDate' => 'required|date|after:RegistrationStartDate'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed..!',
            'errors' => $validator->errors()->all(),
        ], 422));
    }
}
