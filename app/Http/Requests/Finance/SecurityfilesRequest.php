<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class SecurityfilesRequest extends FormRequest
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
                "docTypeId" => "required",
                "proTypeId" => "required",
                "docRef" => "required",
                "docStatus" => "required",
                "issuingAuthority" => "required",
                "CompaniesId" => "required",
                // "Attachement" => "required",
                "docDesc" => "required",
                "docNature" => "required",
                "ownerShipTitle" => "required",
            ];
        }

        return $rules;
    }
}