<?php

namespace App\Http\Livewire\Front\Reservation;

use App\Models\Reservation;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationDataTable extends Component
{
    use WithPagination;

    public bool $editAskCard = false;

    public function render()
    {
        return view('livewire.front.reservation.reservation-data-table', [
            'reservations' => Reservation::paginate(10)
        ])
            ->layout('components.front-layout');
    }

    public function openModel()
    {
        $this->editAskCard = true;
    }
}
