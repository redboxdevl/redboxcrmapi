<?php

namespace App\Http\Requests\Videorequestform;

use Illuminate\Foundation\Http\FormRequest;

class VideoFormRequest extends FormRequest
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
                "video_category_id" => "required",
                "project_id" => "required",
                "video_format" => "required",
                "video_topic" => "required",
                "guest_name" => "required",
                "other_category" => "required",
                "video_topic" => "required",
                "gender" => "required",
                "age_group" => "required",
                "education" => "required",
                "location" => "required",
                "video_outline" => "required",
                "video_instructions" => "required",
                "video_ref_image" => "required",
                "video_ref_ppt" => "required",
                "video_ref_pdf" => "required",
                "video_ref_web" => "required",
                "video_ref_video" => "required",
            ];
        }

        return $rules;
    }
}
