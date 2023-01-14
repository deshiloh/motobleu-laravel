<?php

namespace App\Http\Livewire\Front\Reservation;

use App\Models\Reservation;
use App\Traits\WithReservationForm;
use Livewire\Component;

class ReservationForm extends Component
{
    use WithReservationForm;

    public function mount()
    {
        $this->reservation = new Reservation();
        $this->userId = \Auth::user()->id;
        $this->defaultReset();
    }

    public function render()
    {
        return view('livewire.front.reservation.reservation-form')
            ->layout('components.front-layout');
    }

    public function saveReservation()
    {
        $this->createReservationWithRedirection(route('front.reservation.list'));
    }
}
