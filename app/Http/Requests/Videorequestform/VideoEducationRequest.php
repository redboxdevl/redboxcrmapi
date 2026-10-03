<?php

namespace App\Http\Requests\Videorequestform;

use Illuminate\Foundation\Http\FormRequest;

class VideoEducationRequest extends FormRequest
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
                "education_name" => "required",
                "education_abbr" => "required",
            ];
        }

        return $rules;
    }
}
