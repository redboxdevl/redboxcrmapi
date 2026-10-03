<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
                // "emp_code" => "required",
                "emp_name" => "required",
                "email" => "required",
                "password" => "required",
                "dob" => "required",
                "marital_status" => "required",
                "gender" => "required",
                "joining_date" => "required",
                "company_id" => "required",
                // "phone_number" => "required",
                // "mobile_number" => "required",
                "department_id" => "required",
                "designation_id" => "required",
                "branch_id" => "required",
                "status" => "required",
                // "profile_pic" => "required",
            ];
        }

        return $rules;
    }
}
