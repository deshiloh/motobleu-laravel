<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PassagerRequest extends FormRequest
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
        return [
            'nom' => 'required',
            'telephone' => 'string',
            'portable' => 'string',
            'email' => 'required|email',
            'user_id' => 'required'
        ];
    }
}
