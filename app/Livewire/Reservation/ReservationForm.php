<?php

namespace App\Livewire\Reservation;

use App\Models\Reservation;
use App\Services\EventCalendar\GoogleCalendarService;
use App\Services\ReservationService;
use App\Traits\WithReservationForm;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class ReservationForm extends Component
{
    use WireUiActions, WithReservationForm;

    public function mount(Reservation $reservation): void
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
        }

        $this->defaultReset();
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
     * @param GoogleCalendarService $calendarService
     * @return void
     */
    public function saveReservation(GoogleCalendarService $calendarService): void
    {
        $this->createReservationWithRedirection(route('admin.reservations.index'));

        if (\App::environment(['local', 'beta', 'prod']) && $this->reservation->exists) {
            $calendarService->createEventForSecretary($this->reservation);
        }
    }
}
