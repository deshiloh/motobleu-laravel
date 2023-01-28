<?php

namespace App\Http\Livewire\Reservation;

use App\Services\SentryService;
use App\Traits\WithReservationForm;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Validator;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use WireUi\Traits\Actions;

class ReservationForm extends Component
{
    use Actions, WithReservationForm;

    public function mount(Reservation $reservation)
    {
        $this->reservation = $reservation;

        if ($this->reservation->exists) {
            $this->userId = $this->reservation->passager->user->id;

            if ($this->reservation->adresseReservationFrom()->exists()) {
                $this->pickupMode = ReservationService::WITH_ADRESSE;
            }

            if ($this->reservation->adresseReservationTo()->exists()) {
                $this->dropMode = ReservationService::WITH_ADRESSE;
            }

            if ($this->reservation->reservationBack()->exists()) {
                $this->hasBack = true;
                $this->reservation_back = Reservation::find($this->reservation->reservationBack->id);

                if ($this->reservation_back->adresseReservationFrom()->exists()) {
                    $this->backPickupMode = ReservationService::WITH_ADRESSE;
                }

                if ($this->reservation_back->adresseReservationTo()->exists()) {
                    $this->backPickupMode = ReservationService::WITH_ADRESSE;
                }
            }

        } else {
            $this->defaultReset();
        }

    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.reservation.reversation-form')
            ->layout('components.layout');
    }

    /**
     * @return void
     */
    public function saveReservation(): void
    {
        $this->createReservationWithRedirection(route('admin.reservations.index'));
    }
}
