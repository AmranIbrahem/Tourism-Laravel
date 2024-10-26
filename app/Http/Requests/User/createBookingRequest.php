<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class createBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bookingDate' => 'required|date',
            'startDate' => 'required|date_format:H:i',
            'endDate' => 'required|date_format:H:i|after:startTime'
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
