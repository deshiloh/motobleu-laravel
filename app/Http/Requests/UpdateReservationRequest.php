<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'secretary_id' => ['required', 'integer', 'exists:users,id'],
            'company_id' => ['required', 'integer', 'exists:entreprises,id'],
            'pickup_date' => ['required', 'date'],
            'localisation_from' => ['nullable', 'integer', 'exists:localisations,id'],
            'localisation_to' => ['nullable', 'integer', 'exists:localisations,id'],
            'address_from' => ['nullable', 'integer', 'exists:adresse_reservations,id'],
            'address_to' => ['nullable', 'integer', 'exists:adresse_reservations,id'],
            'steps' => ['nullable'],
            'calendar_passager_invitation' => ['boolean'],
            'send_to_passager' => ['boolean'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'secretary_id.exists' => 'L\'utilisateur n\'existe pas',
            'secretary_id.required' => 'L\'utilisateur est requis',
            'company_id.exists' => 'L\'entreprise n\'existe pas',
            'company_id.required' => 'Aucune entreprise n\'a été sélectionnée',
            'localisation_from.exists' => 'Le lieu de prise en charge n\'existe pas',
            'localisation_to.exists' => 'Le lieu de destination n\'existe pas',
            'address_from.exists' => 'L\'adresse de prise en charge n\'existe pas',
            'address_to.exists' => 'L\'adresse de destination n\'existe pas',
        ];
    }
}
