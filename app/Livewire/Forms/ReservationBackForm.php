<?php

namespace App\Livewire\Forms;

use App\Models\Reservation;
use Livewire\Form;

class ReservationBackForm extends Form
{
    public ?string $pickupDate = null;

    public function backRules(): array
    {
        return [

        ];
    }

    public function createReservationWithoutValidation(): Reservation
    {
        // TODO: Implémenter la création de la réservation retour
        return Reservation::create([]);
    }
}
