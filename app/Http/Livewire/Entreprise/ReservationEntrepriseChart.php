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
        $period = \Carbon\CarbonPeriod::create(\Carbon\Carbon::now()->startOfYear(), '1 month', \Carbon\Carbon::now()->endOfMonth());
        $data = [];

        foreach ($period as $date)
        {
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
