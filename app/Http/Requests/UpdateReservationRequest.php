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
            'company_id' => ['required', 'integer', 'exists:entreprises,id'],
            'passenger_id' => ['required', 'integer', 'exists:passagers,id'],
            'pickup_date' => ['required', 'date'],
            'localisation_from_id' => ['nullable', 'integer', 'exists:localisations,id'],
            'localisation_to_id' => ['nullable', 'integer', 'exists:localisations,id'],
            'address_from_id' => ['nullable', 'integer', 'exists:adresse_reservations,id'],
            'address_to_id' => ['nullable', 'integer', 'exists:adresse_reservations,id'],
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
            'passenger_id.exists' => 'Le passager n\'existe pas',
            'passenger_id.required' => 'Le passager est obligatoire',
            'company_id.exists' => 'L\'entreprise n\'existe pas',
            'company_id.required' => 'L\'entreprise est obligatoire',
            'localisation_from_id.exists' => 'Le lieu de prise en charge n\'existe pas',
            'localisation_to_id.exists' => 'Le lieu de destination n\'existe pas',
            'address_from_id.exists' => 'L\'adresse de prise en charge n\'existe pas',
            'address_to_id.exists' => 'L\'adresse de destination n\'existe pas',
        ];
    }
}
