<?php

namespace App\Http\Livewire\Home;

use App\Models\Reservation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Livewire;

class ReservationsChart extends Component
{
    public array $dataset = [];

    public function mount()
    {
        $this->dataset = $this->getDatas();
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.home.reservations-chart');
    }

    private function getLabels(): array
    {
        $period = now()->subMonths(6)->monthsUntil(now());
        $labels = [];

        foreach ($period as $date) {
            $labels[] = $date->shortMonthName . ' ' .$date->year;
        }

        return $labels;
    }

    private function getDatas(): array
    {
        $period = now()->subMonths(6)->monthsUntil(now());
        $data = [];

        foreach ($period as $date)
        {
            $nbReservation = Reservation::whereMonth('pickup_date', $date->month)->whereYear('pickup_date', $date->year)->count();

            $data[] = [
                'date' => $date->shortMonthName . ' ' .$date->year,
                'count' => $nbReservation
            ];
        }

        return $data;
    }

    public function reloadData()
    {
        $dataset = $this->getDatas();
        $this->emit('updateChart', $dataset);
    }
}
