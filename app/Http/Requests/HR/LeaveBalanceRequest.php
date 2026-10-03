<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class LeaveBalanceRequest extends FormRequest
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
                "casual_leaves" => "required",
                "sick_leaves" => "required",
                "annual_leaves" => "required",
                "paternity_leaves" => "required",
                "maternity_leaves" => "required",
                "unpaid_leaves" => "required",
            ];
        }

        return $rules;
    }
}
