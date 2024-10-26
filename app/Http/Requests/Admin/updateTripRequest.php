<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class updateTripRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'AreaNames' => 'sometimes|array',
            'AreaNames.*' => 'sometimes|exists:areas,AreaName',
            'NumberOfPeople' => 'sometimes|integer',
            'Cost' => 'sometimes|numeric',
            'TripDetails' => 'sometimes|string',
            'TripHistory' => 'sometimes|string',
            'RegistrationStartDate' => 'sometimes|date|before:RegistrationEndDate',
            'RegistrationEndDate' => 'sometimes|date|after:RegistrationStartDate'
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
