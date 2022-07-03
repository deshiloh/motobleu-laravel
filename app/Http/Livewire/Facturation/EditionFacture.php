<?php

namespace App\Http\Livewire\Facturation;

use App\Models\Entreprise;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Component;

class EditionFacture extends Component
{
    public $entrepriseId = 0;
    public int $month;
    public int $year;
    public int $perPage = 10;
    public array $facturesItem = [];

    public function mount()
    {
        $currentDate = Carbon::now();
        $this->month = $currentDate->month;
        $this->year = $currentDate->year;
    }

    public function getReservationsProperty()
    {
        $reservations = Reservation::query()
            ->join('passagers', 'reservations.passager_id', 'passagers.id')
            ->join('users', 'passagers.user_id', 'users.id')
            ->join('entreprises', 'users.entreprise_id', 'entreprises.id')
            ->whereMonth('reservations.pickup_date', $this->month)
            ->whereYear('reservations.pickup_date', $this->year)
            ->where('reservations.is_confirmed', true)
        ;

        if ($this->entreprise) {
            $reservations->where('entreprises.id', $this->entreprise->id);
        }

        ray()->showQueries();

        return $reservations->paginate($this->perPage);
    }

    public function getEntrepriseProperty()
    {
        return !empty($this->entrepriseId) ? Entreprise::find($this->entrepriseId) : null;
    }

    protected function getRules()
    {
        return [
            'entrepriseId' => 'required',
            'facturesItem.*.total' => 'nullable',
            'facturesItem.*.tarif' => 'nullable',
            'facturesItem.*.majoration' => 'nullable',
            'facturesItem.*.complement' => 'nullable',
            'facturesItem.*.comment' => 'nullable',
        ];
    }

    public function render()
    {
        return view('livewire.facturation.edition-facture')
            ->layout('components.admin-layout');
    }

    public function validation(int $item)
    {
        $total = 10;
        $this->facturesItem[$item]['total'] = $total;
    }
}
