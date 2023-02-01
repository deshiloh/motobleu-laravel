<?php

namespace App\Http\Livewire\Pilote;

use App\Models\Pilote;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class RecapReservationPilote extends Component
{
    use WithPagination;

    public Pilote $pilote;
    public ?Reservation $reservationSelected;
    public bool $editReservationMode = false;
    public $reservations = [];
    public $dateDebut;
    public $dateFin;
    public int $perPage = 10;
    protected $queryString = ['dateDebut', 'dateFin'];

    protected array $rules = [
        'reservationSelected.tarif_pilote' => 'required',
        'reservationSelected.majoration_pilote' => 'nullable',
        'reservationSelected.encaisse_pilote' => 'nullable',
        'reservationSelected.encompte_pilote' => 'nullable'
    ];

    /**
     * @param Pilote $pilote
     * @return void
     */
    public function mount(Pilote $pilote): void
    {
        $this->pilote = $pilote;
        $this->dateDebut = $this->dateDebut ?? Carbon::today()->startOfMonth()->addHour();
        $this->dateFin = $this->dateFin ?? Carbon::today()->endOfMonth();
        $this->reservations = $this->getReservations();
        $this->reservationSelected = null;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.pilote.recap-reservation-pilote')
            ->layout('components.layout');
    }

    private function getReservations()
    {
        return Reservation::where('pilote_id', $this->pilote->id)
            ->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin])
            ->orderBy('pickup_date', 'desc')
            ->get();
    }

    public function searchReservations()
    {
        $this->reservations = $this->getReservations();
    }

    public function editReservation(Reservation $reservation)
    {
        $this->reservationSelected = $reservation;
        $this->editReservationMode = true;
    }

    public function closeEditReservation()
    {
        $this->editReservationMode = false;
        $this->reservationSelected = null;
    }

    public function save()
    {
        $this->validate();

        $this->reservationSelected->update();

        $this->reservationSelected = null;
        $this->editReservationMode = false;

        $this->reservations = $this->getReservations();
    }
}
