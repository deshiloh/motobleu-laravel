<?php

namespace App\Http\Livewire\Reservation;

use App\Models\AdresseReservation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AdressesReservationDataTable extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public function render(): Factory|View|Application
    {
        return view('livewire.reservation.adresses-reservation-data-table', [
            'adresses' => AdresseReservation::search($this->search)
                ->paginate($this->perPage)
        ]);
    }
}
