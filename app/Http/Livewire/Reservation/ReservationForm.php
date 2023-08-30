<?php

namespace App\Http\Livewire\Reservation;

use App\Models\AdresseReservation;
use app\Settings\BillSettings;
use App\Traits\WithReservationForm;
use App\Models\Reservation;
use App\Services\ReservationService;
use Livewire\Component;
use WireUi\Traits\Actions;

class ReservationForm extends Component
{
    use Actions, WithReservationForm;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation;

        if ($this->reservation->exists) {
            $this->userId = $this->reservation->passager->user->id;

            if ($this->reservation->adresseReservationFrom()->exists()) {
                $this->pickupMode = ReservationService::WITH_ADRESSE;
            } else {
                $this->newAdresseReservationFrom = new AdresseReservation();
            }

            if ($this->reservation->adresseReservationTo()->exists()) {
                $this->dropMode = ReservationService::WITH_ADRESSE;
            } else {
                $this->newAdresseReservationTo = new AdresseReservation();
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
