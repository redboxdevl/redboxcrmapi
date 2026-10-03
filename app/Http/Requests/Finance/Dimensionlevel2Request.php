<?php

namespace App\Http\Requests\Finance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class Dimensionlevel2Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() : array
    {
        $rules = [];

        if (in_array($this->method(), ['POST', 'PUT', 'PATCH'])) {
            $rules = [
                "level1Code" => "required",
                "Code" => "required|digits:3",
                "Description" => "required",
            ];
        }

        return $rules;
    }
    
     protected function failedValidation(Validator $validator)
        {
            $error = collect($validator->errors())->collapse()->toArray();
            $errors = implode(' | ', $error);
            throw new HttpResponseException(response()->json(
                ['response' => ['status' => false, 'message' => $errors]],
                Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    
}