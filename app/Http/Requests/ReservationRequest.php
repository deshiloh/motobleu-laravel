<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            'entreprise_id' => ['required', 'exists:entreprises,id'],
            'passager_id' => ['required', 'integer', 'exists:passagers,id'],
            'pickup_date' => ['required', 'date', 'date_format:Y-m-d H:i'],
            'location_from_id' => ['nullable', 'integer', 'exists:localisations,id'],
            'location_to_id' => ['nullable', 'integer', 'exists:localisations,id'],
            'address_from_id' => ['nullable', 'integer', 'exists:adresse_reservations,id'],
            'address_to_id' => ['nullable', 'integer', 'exists:adresse_reservations,id'],
            'steps' => ['nullable', 'string'],
            'comment' => ['nullable', 'string'],
            'calendar_passager_invitation' => ['boolean'],
            'send_to_passager' => ['boolean']
        ];
    }
}
