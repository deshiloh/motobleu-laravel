<?php

namespace App\Http\Livewire\Home;

use App\Models\Entreprise;
use Livewire\Component;

class CompanyReservationStats extends Component
{
    public Entreprise $entreprise;
    public bool $isLast = false;

    public function mount()
    {
        $this->entreprise = $this->getFirstCompanyReservation();
    }

    public function getFirstCompanyReservation(): Entreprise
    {
        return Entreprise::withCount('reservations')
            ->orderBy('reservations_count', ($this->isLast) ? 'ASC' : 'DESC')
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
