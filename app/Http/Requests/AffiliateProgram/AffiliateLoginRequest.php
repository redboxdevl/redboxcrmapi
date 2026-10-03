<?php

namespace App\Http\Requests\AffiliateProgram;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateLoginRequest extends FormRequest
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
        $rules = [];

        if (in_array($this->method(), ['POST', 'PUT', 'PATCH'])) {
            $rules = [
                "email_address" => "required",
                "password" => "required",
            ];
        }

        return $rules;
    }
}
