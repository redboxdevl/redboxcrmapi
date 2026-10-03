<?php

namespace App\Http\Requests\AffiliateProgram;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateActivityRequest extends FormRequest
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
                'affiliate_lead_id'=>'required',
                'typeId'=>'required',
                'outcomeId'=>'required',
                'InterestPercentage'=>'required',
                'activityDetails'=>'required',
                'actStatus'=>'required'
            ];
        }

        return $rules;
    }
}
