<?php

namespace App\Http\Livewire\Front\Reservation;

use Livewire\Component;

class ReservationDataTable extends Component
{
    public function render()
    {
        return view('livewire.front.reservation.reservation-data-table')
            ->layout('components.front-layout');
    }
}
