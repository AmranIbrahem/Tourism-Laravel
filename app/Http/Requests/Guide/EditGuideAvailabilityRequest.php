<?php

namespace App\Http\Requests\Guide;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditGuideAvailabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'availableDate' => 'sometimes|date',
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
