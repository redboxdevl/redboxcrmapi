<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class TrainingRequest extends FormRequest
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
                "trainee_name" => "required",
                "department_id" => "required",
                "training_type_id" => "required",
                "training_title" => "required",
                "description" => "required",
                "from_date" => "required",
                "to_date" => "required",
                "start_time" => "required",
                "end_time" => "required",
                "status" => "required",
                // "attachement" => "required",
            ];
        }

        return $rules;
    }
}
