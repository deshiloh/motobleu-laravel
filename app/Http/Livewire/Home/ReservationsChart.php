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
    public EntrepriseModel $firstEntreprise;
    public EntrepriseModel $lastEntreprise;
    public int $nbTotalReservation = 0;

    public function mount()
    {
        $this->dataset = $this->getDatas();
        $this->firstEntreprise = $this->getCompanyBorderNbReservationOrder(Entreprise::FIRST->value);
        $this->lastEntreprise = $this->getCompanyBorderNbReservationOrder(Entreprise::LAST->value);
        $this->nbTotalReservation = Reservation::count();
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
     * @return EntrepriseModel
     */
    public function getCompanyBorderNbReservationOrder(string $order): EntrepriseModel
    {
        return EntrepriseModel::withCount('reservations')
            ->where('is_actif', 1)
            ->orderBy('reservations_count', $order)
            ->first();
    }

    public function reloadData()
    {
        $this->nbTotalReservation = Reservation::count();
        $dataset = $this->getDatas();
        $this->emit('updateChart', $dataset);
    }
}
