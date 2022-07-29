<?php

namespace App\Http\Livewire\Reservation;

use App\Events\ReservationCanceled;
use App\Events\ReservationConfirmed;
use App\Models\Pilote;
use App\Models\Reservation;
use Livewire\Component;

class ReservationShow extends Component
{
    public Reservation $reservation;
    public string $message;

    public function mount(Reservation $reservation)
    {
        $this->reservation = $reservation;

        $this->message = "Bonjour,
Votre réservation a bien été prise en compte
Cordialement.";
    }

    public function render()
    {
        return view('livewire.reservation.reservation-show')
            ->layout('components.layout');
    }

    protected function getRules()
    {
        return [
            'reservation.pilote_id' => 'required',
            'reservation.send_to_user' => 'boolean',
            'reservation.send_to_passager' => 'boolean',
            'reservation.calendar_passager_invitation' => 'boolean',
            'reservation.calendar_user_invitation' => 'boolean',
            'message' => 'required|string',
        ];
    }

    protected function getValidationAttributes()
    {
        return [
            'reservation.pilote_id' => 'pilote'
        ];
    }

    public function confirmedAction()
    {
        $this->validate();

        $this->reservation->is_confirmed = true;

        $this->reservation->pilote()->associate(Pilote::find($this->reservation->pilote_id));

        $this->reservation->update([
            'is_confirmed' => $this->reservation->is_confirmed,
        ]);

        ReservationConfirmed::dispatch($this->reservation);
    }

    public function cancelAction()
    {
        $this->reservation->is_cancel = true;
        $this->reservation->is_confirmed = false;

        $this->reservation->update([
            'is_cancel' => true,
            'is_confirmed' => false
        ]);

        ReservationCanceled::dispatch($this->reservation);
    }
}
