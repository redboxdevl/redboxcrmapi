<?php

namespace App\Http\Requests\TaskManagement;

use Illuminate\Foundation\Http\FormRequest;

class TaskAttachmentRequest extends FormRequest
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
                'task_id_pk'=>'required',
                'emp_id'=>'required',
                'comment_id'=>'required',
                'attachement'=>'required',
                'attachement_date_time'=>'required',
            ];
        }

        return $rules;
    }
}
