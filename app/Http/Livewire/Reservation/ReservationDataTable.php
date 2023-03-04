<?php

namespace App\Http\Livewire\Reservation;

use App\Enum\ReservationStatus;
use App\Models\Reservation;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationDataTable extends Component
{
    use WithSorting, WithPagination;

    public string $search = '';
    public int $perPage = 100;
    public string $sortField = 'id';
    protected $queryString = ['querySort' => ['except' => '']];
    public string $querySort = '';
    public array $listPerPage = [
        20,
        50,
        100,
        200,
        300
    ];

    public function mount()
    {
        $this->sortDirection = 'desc';
    }

    public function render(): Factory|View|Application
    {
        $reservations = Reservation::query()
            ->when($this->search, function (Builder $query, $search) {
                return $query->where('reference', 'like', '%'.$search . '%');
            })
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
