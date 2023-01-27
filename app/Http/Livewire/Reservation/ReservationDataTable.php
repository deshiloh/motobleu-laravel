<?php

namespace App\Http\Livewire\Reservation;

use App\Enum\ReservationStatus;
use App\Models\Reservation;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationDataTable extends Component
{
    use WithSorting, WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'id';
    protected $queryString = ['querySort' => ['except' => '']];
    public string $querySort = '';

    public function mount()
    {
        $this->sortDirection = 'desc';
    }

    public function render()
    {
        $reservations = Reservation::search($this->search)
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->querySort == 'not_confirmed') {
            $reservations
                ->where('statut', ReservationStatus::Created)
            ;
        }

        return view('livewire.reservation.reservation-data-table', [
            'reservations' => $reservations->paginate($this->perPage),
            'countReservationToConfirmed' => Reservation::toConfirmed()->count()
        ]);
    }
}
