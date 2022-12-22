<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Models\Reservation;
use Livewire\Component;

class ReservationEntrepriseChart extends Component
{
    public Entreprise $entreprise;
    public array $dataset;

    public function mount(Entreprise $entreprise)
    {
        $this->entreprise = $entreprise;
        $this->dataset = $this->getDatas();
    }
    public function render()
    {
        return view('livewire.entreprise.reservation-entreprise-chart');
    }

    private function getDatas(): array
    {
        $period = now()->subMonths(6)->monthsUntil(now());
        $data = [];

        foreach ($period as $date)
        {
            ray()->showQueries();
            $nbReservation = Reservation::where('entreprise_id', $this->entreprise->id)
                ->whereMonth('pickup_date', $date->month)
                ->whereYear('pickup_date', $date->year)
                ->count();

            $data[] = [
                'date' => $date->shortMonthName . ' ' .$date->year,
                'count' => $nbReservation
            ];
        }

        return $data;
    }
}
