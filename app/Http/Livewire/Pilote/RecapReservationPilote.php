<?php

namespace App\Http\Livewire\Pilote;

use App\Models\Pilote;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\WithPagination;

class RecapReservationPilote extends Component
{
    use WithPagination;

    public Pilote $pilote;
    public $dateDebut;
    public $dateFin;
    public int $perPage = 10;

    protected $queryString = ['dateDebut', 'dateFin'];

    /**
     * @param Pilote $pilote
     * @return void
     */
    public function mount(Pilote $pilote): void
    {
        $this->pilote = $pilote;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        $reservations = Reservation::query()
            ->where('pilote_id', $this->pilote->id);

        if ($this->dateDebut && $this->dateFin) {
            $reservations->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin]);
        }

        return view('livewire.pilote.recap-reservation-pilote', [
            'reservations' => $reservations->paginate($this->perPage)
        ])->layout('components.admin-layout');
    }
}
