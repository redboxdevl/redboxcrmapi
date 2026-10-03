<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class AssetRegisterRequest extends FormRequest
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
                "assetTypeId" => "required",
                "purchasedDate" => "required",
                // "AssetBookValue" => "required",
                "assetLifeId" => "required",
                // "Location" => "required",
                "CompanyId" => "required",
                "DepartmentId" => "required",
                "Quantity" => "required",
                // "AssetImage" => "required",
                "AssetDesc" => "required",
            ];
        }

        return $rules;
    }
}