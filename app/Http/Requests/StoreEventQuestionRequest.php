<?php

namespace App\Http\Requests;

class StoreEventQuestionRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        return [
            'title'  => 'required',
            'option' => 'array',
            'is_required' => 'string',
            'question_type_id' => 'integer',
            'tickets' => 'array',
        ];
    }
}
