<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
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
                "emp_id" => "required",
                "old_dept_id" => "required",
                "new_dept_id" => "required",
                "old_designation_id" => "required",
                "new_designation_id" => "required",
                "old_salary" => "required",
                "new_salary" => "required",
                "description" => "required",
                "promotion_date" => "required",
            ];
        }

        return $rules;
    }
}
