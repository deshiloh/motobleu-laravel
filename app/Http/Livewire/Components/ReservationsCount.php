<?php

namespace App\Http\Livewire\Components;

use App\Models\Reservation;
use Livewire\Component;

class ReservationsCount extends Component
{
    public int $nbTotalReservation = 0;

    public function mount()
    {
        $this->nbTotalReservation = Reservation::count();
    }

    public function render()
    {
        return view('livewire.components.reservations-count');
    }

    public function refreshCount()
    {
        $this->nbTotalReservation = Reservation::count();
    }
}
