<?php

namespace App\Http\Livewire\Home;

use App\Enum\Entreprise;
use App\Models\Entreprise as EntrepriseModel;
use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.home.reservations-chart');
    }

    private function getLabels(): array
    {
        $period = $this->getPeriod();
        $labels = [];

        foreach ($period as $date) {
            $labels[] = $date->shortMonthName . ' ' .$date->year;
        }

        return $labels;
    }

    private function getDatas(): array
    {
        $period = $this->getPeriod();
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
        $dataset = $this->getDatas();
        $this->emit('updateHomeReservationChart', $dataset);
    }

    private function getPeriod()
    {
        return CarbonPeriod::create(Carbon::now()->startOfYear(), '1 month', Carbon::now()->endOfMonth());
    }
}
