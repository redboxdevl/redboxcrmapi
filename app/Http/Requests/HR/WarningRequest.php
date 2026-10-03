<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class WarningRequest extends FormRequest
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
                "subject" => "required",
                "type" => "required",
                "description" => "required",
                "warning_date" => "required",
                "warning_by" => "required",
            ];
        }

        return $rules;
    }
}
