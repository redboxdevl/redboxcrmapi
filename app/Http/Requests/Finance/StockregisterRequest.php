<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StockregisterRequest extends FormRequest
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
                "stockIndate" => "required",
                "projectid" => "required",
                "stocknatureid" => "required",
                "stocktypeid" => "required",
                "quantityIn" => "required",
                "stockrecby" => "required",
                // "Attachement" => "required",
                "stockdeliveredby" => "required",
            ];
        }

        return $rules;
    }
}