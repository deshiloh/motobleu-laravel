<?php

namespace App\Http\Livewire\Home;

use App\Models\Entreprise;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CompanyReservationStats extends Component
{
    public Entreprise $entreprise;
    public bool $isLast = false;

    public function mount()
    {
        $this->entreprise = $this->getFirstCompanyReservation();
    }

    public function getFirstCompanyReservation()
    {
        return Entreprise::withCount([
            'reservations' => function (Builder $query) {
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfMonth();

                $query->whereBetween('pickup_date', [$startDate, $endDate]);
            }
        ])->orderBy('reservations_count',
                $this->isLast ? \App\Enum\Entreprise::LAST->value : \App\Enum\Entreprise::FIRST->value)
            ->first();

    }

    public function render()
    {
        return view('livewire.home.company-reservation-stats');
    }

    public function reloadEntreprise()
    {
        $this->entreprise = $this->getFirstCompanyReservation();
    }
}
