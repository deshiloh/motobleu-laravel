<?php

namespace App\Http\Livewire\Reservation;

use App\Models\AdresseReservation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class AdressesReservationDataTable extends Component
{
    use WithPagination, Actions;

    public string $search = '';
    public int $perPage = 20;

    public function render(): Factory|View|Application
    {
        return view('livewire.reservation.adresses-reservation-data-table', [
            'adresses' => AdresseReservation::when($this->search, function (Builder $query, $search) {
                $query->where('adresse', 'LIKE', '%'.$search.'%');
            })
                ->orderBy('adresse')
                ->paginate($this->perPage)
        ]);
    }

    /**
     * Permet de désactiver l'adresse de réservation sélectionnée.
     * @param AdresseReservation $address
     * @return void
     */
    public function disableAddress(AdresseReservation $address): void
    {
        $address->is_actif = false;
        $address->update();

        $this->notification()->success(
            "Opération réussite",
            "L'adresse a bien été désactivée."
        );
    }

    /**
     * Permet d'activer l'adresse de réservation sélectionnée.
     * @param AdresseReservation $address
     * @return void
     */
    public function enableAddress(AdresseReservation $address): void
    {
        $address->is_actif = true;
        $address->update();

        $this->notification()->success(
            "Opération réussite",
            "L'adresse a bien été activée."
        );
    }

    public function toggleDeleteAddress(AdresseReservation $address)
    {
        $address->is_deleted = !$address->is_deleted;
        $address->update();

        $this->notification()->success(
            "Opération réussite",
            $address->is_deleted ? "L'adresse a bien été supprimée." : "L'adresse a bien été ajoutée."
        );
    }
}
