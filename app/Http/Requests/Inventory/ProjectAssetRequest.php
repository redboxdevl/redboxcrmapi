<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ProjectAssetRequest extends FormRequest
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
                "project_id" => "required",
                "asset_name" => "required",
                "project_asset_path" => "required",
                "asset_type" => "required",
            ];
        }

        return $rules;
    }
}
