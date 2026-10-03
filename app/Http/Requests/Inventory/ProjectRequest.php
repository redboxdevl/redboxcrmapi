<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
                "project_name" => "required",
                "location_id" => "required",
                "country_id" => "required",
                "city_id" => "required",
                "address" => "required",
                "commission" => "required",
                "kam" => "required",
                "category" => "required",
                "projectLogo" => "required",
            ];
        }

        return $rules;
    }
}
