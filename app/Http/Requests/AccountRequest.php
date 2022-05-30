<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
        $rules =  [
            'nom' => ['required', 'string'],
            'prenom' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'telephone' => ['required'],
            'adresse' => ['required', 'string'],
            'adresse_bis' => ['required', 'string'],
            'code_postal' => ['required'],
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            /** @var User $account */
            $account = $this->route()->parameter('account');
            if ($this->input('email') == $account->email) {
                $rules['email'] = ['required', 'email'];
            }
        }

        return $rules;
    }
}
