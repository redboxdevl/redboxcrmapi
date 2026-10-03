<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventorySoldRequest extends FormRequest
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
                "inventory_id" => "required",
                "customer_id" => "required",
                "employee_id" => "required",
                "father_name" => "required",
                "date_of_birth" => "required",
                "client_address" => "required",
                "client_image" => "required",
                "cnic" => "required",
                "cnic_front_image" => "required",
                "cnic_back_image" => "required",
                "relationship_title" => "required",
                "nominee_name" => "required",
                "nominee_image" => "required",
                "nominee_cnic" => "required",
                "nominee_cell" => "required",
                "nominee_cnic_front" => "required",
                "nominee_cnic_back" => "required",
                "nominee_address" => "required",
                // "discount_amount" => "required",
                // "after_discount_amount" => "required",
                // "received_amount" => "required",
                // "payment_type" => "required",
                // "cheque_no" => "required",
                // "mode_of_payment" => "required",
                // "reference_type" => "required",
                // "bank_name" => "required",
                
            ];
        }

        return $rules;
    }
}
