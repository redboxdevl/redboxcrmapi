<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
                "machine_id" => "required",
                "location_lat" => "required",
                "location_long" => "required",
                "checkout_location_lat" => "required",
                "checkout_location_long" => "required",
                "is_mobile" => "required",
                "attendance_date" => "required",
                "checkin" => "required",
                "checkout" => "required",
                "checkintype" => "required",
                "checkouttype" => "required",
            ];
        }

        return $rules;
    }
}
