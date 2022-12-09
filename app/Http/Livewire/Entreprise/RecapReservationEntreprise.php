<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\WithPagination;

class RecapReservationEntreprise extends Component
{
    use WithPagination;

    public Entreprise $entreprise;
    public $dateDebut;
    public $dateFin;
    public int $perPage = 10;
    protected $queryString = ['dateDebut', 'dateFin'];

    public function render()
    {
        return view('livewire.entreprise.recap-reservation-entreprise', [
            'reservations' => Reservation::paginate($this->perPage)
        ]);

        /*
        $reservations = Reservation::query()
            ->join('passagers', 'reservations.passager_id', '=', 'passagers.id')
            ->join('users', 'passagers.user_id', '=', 'users.id')
            ->join('entreprises', 'users.entreprise_id', '=', 'entreprises.id')
            ->where('entreprises.id', "=", $this->entreprise->id);

        if ($this->dateDebut && $this->dateFin) {
            $reservations->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin]);
        }

        return view('livewire.entreprise.recap-reservation-entreprise', [
            'reservations' => $reservations->paginate($this->perPage)
        ]);*/
    }
}
