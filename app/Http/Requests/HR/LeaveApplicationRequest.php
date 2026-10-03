<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class LeaveApplicationRequest extends FormRequest
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
                "leave_type_id" => "required",
                "emp_id" => "required",
                "from_date" => "required",
                "to_date" => "required",
                "description" => "required",
                "no_of_days" => "required",
                "status" => "required",
            ];
        }

        return $rules;
    }
}
