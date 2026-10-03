<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class JobPostRequest extends FormRequest
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
                "job_title" => "required",
                "description" => "required",
                "status" => "required",
                "published_date" => "required",
                "deadline" => "required",
            ];
        }

        return $rules;
    }
}
