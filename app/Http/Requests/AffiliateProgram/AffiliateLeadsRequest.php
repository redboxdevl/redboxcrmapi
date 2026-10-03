<?php

namespace App\Http\Requests\AffiliateProgram;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateLeadsRequest extends FormRequest
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
                "affiliate_id" => "required",
                "countryid" => "required",
                "cityid" => "required",
                "projectid" => "required",
                "typeid" => "required",
                "natureid" => "required",
                "actMediumId" => "required",
                "clientName" => "required",
                "clientStatus" => "required",
                "Gender" => "required",
                "email" => "required",
                "mobileNo" => "required",
                "passportCnic" => "required",
                "whatsappNo" => "required",
                "prefLanguage" => "required",
                "Budget" => "required",
                "timeFrame" => "required",
                "RefferdBy" => "required",
                "clientInterst" => "required",
                "clientActivityDesc" => "required",
            ];
        }

        return $rules;
    }
}
