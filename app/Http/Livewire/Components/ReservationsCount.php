<?php

namespace App\Http\Livewire\Components;

use App\Enum\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ReservationsCount extends Component
{
    public int $nbTotalReservation = 0;

    public function mount()
    {
        $this->nbTotalReservation = $this->getDatas();
    }

    public function render()
    {
        return view('livewire.components.reservations-count');
    }

    public function refreshCount()
    {
        $this->nbTotalReservation = $this->getDatas();
    }

    /**
     * Récupère le nombre de réservations sur l'année en cours
     * @return mixed
     */
    private function getDatas()
    {
        return Reservation::whereBetween('pickup_date', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfMonth()
        ])
            ->where(function(Builder $query) {
                $query
                    ->whereNull('encaisse_pilote')
                    ->orWhere('encaisse_pilote', 0);
            })
            ->whereIn('statut', [
                ReservationStatus::Billed,
                ReservationStatus::CanceledToPay
            ])
            ->count();
    }
}
