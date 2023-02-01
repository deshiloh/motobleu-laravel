<?php

namespace App\Http\Livewire\Pilote;

use App\Enum\ReservationStatus;
use App\Models\Pilote;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class RecapReservationPilote extends Component
{
    use WithPagination, Actions;

    public Pilote $pilote;
    public $reservations = [];
    public $dateDebut;
    public $dateFin;
    public int $perPage = 10;
    protected $queryString = ['dateDebut', 'dateFin'];
    protected $listeners = [
        'eventTest' => 'myTestEvent'
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
            ->where('statut', ReservationStatus::Confirmed->value)
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

    public function myTestEvent(array $datas)
    {
        $validator = \Validator::make($datas, [
            'tarif' => 'required',
            'majoration' => 'nullable',
            'encaisse' => 'nullable',
            'encompte' => 'nullable',
            'reservation' => 'required'
        ]);

        if ($validator->fails()) {
            $description = implode('<br>', $validator->errors()->all());
            $this->notification()->error('Erreur', $description);
            return false;
        }

        $reservation = Reservation::find($datas['reservation']);
        $reservation->update([
            'tarif_pilote' => $datas['tarif'],
            'majoration_pilote' => $datas['majoration'],
            'encaisse_pilote' => $datas['encaisse'],
            'encompte_pilote' => $datas['encompte'],
        ]);

        $this->notification()->success('test');

        $this->reservations = $this->getReservations();
    }
}
