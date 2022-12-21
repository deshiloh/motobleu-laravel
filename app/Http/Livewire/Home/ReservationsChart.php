<?php

namespace App\Http\Livewire\Home;

use App\Enum\Entreprise;
use App\Models\Entreprise as EntrepriseModel;
use App\Models\Reservation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;


class ReservationsChart extends Component
{
    public array $dataset = [];
    public string $firstEntrepriseName;
    public string $lastEntrepriseName;

    public function mount()
    {
        $this->dataset = $this->getDatas();
        $this->firstEntrepriseName = $this->getCompanyBorderNbReservationOrder(Entreprise::FIRST->value);
        $this->lastEntrepriseName = $this->getCompanyBorderNbReservationOrder(Entreprise::LAST->value);
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
            $nbReservation = Reservation::whereMonth('pickup_date', $date->month)
                ->whereYear('pickup_date', $date->year)
                ->count();

            $data[] = [
                'date' => $date->shortMonthName . ' ' .$date->year,
                'count' => $nbReservation
            ];
        }

        return $data;
    }

    /**
     * Récupère le nom de l'entreprise qui a le plus ou le moins de réservations selon le paramètre
     * @param string $order
     * @return string
     */
    public function getCompanyBorderNbReservationOrder(string $order): string
    {
        $entreprise = EntrepriseModel::withCount('reservations')
            ->where('is_actif', 1)
            ->orderBy('reservations_count', $order)
            ->first();

        return $entreprise['nom'];
    }

    public function reloadData()
    {
        $dataset = $this->getDatas();
        $this->emit('updateChart', $dataset);
    }
}
