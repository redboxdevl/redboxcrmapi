<?php

namespace App\Http\Requests\AffiliateProgram;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateRegisterRequest extends FormRequest
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
                "title" => "required",
                "full_name" => "required",
                "cell_no" => "required",
                "email_address" => "required",
                "business_name" => "required",
                "address" => "required",
                "emp_id" => "required",
                "Password"=> "required",
            ];
        }

        return $rules;
    }
}
