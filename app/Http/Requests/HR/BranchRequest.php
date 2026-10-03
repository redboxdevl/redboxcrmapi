<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
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
                "branch_name" => "required|string|max:200",
                "company_id" => "required",
                "country_id" => "required",
                "city_id" => "required",
                "postal_code" => "integer",
                "address" => "required|string|max:200",
                "contact_number" => "required|string|max:200",
            ];
        }

        return $rules;
    }
}
