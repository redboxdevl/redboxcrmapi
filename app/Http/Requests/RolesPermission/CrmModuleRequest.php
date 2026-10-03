<?php

namespace App\Http\Requests\RolesPermission;

use Illuminate\Foundation\Http\FormRequest;

class CrmModuleRequest extends FormRequest
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
                "module_name" => "required",
            ];
        }

        return $rules;
    }
}
