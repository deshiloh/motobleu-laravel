<?php

namespace App\Http\Livewire\Home;

use App\Models\Facture;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class HomeFacturationChart extends Component
{
    public array $dataset = [];

    public function mount()
    {
        $this->dataset = $this->getDatas();
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.home.home-facturation-chart');
    }

    public function getDatas(): array
    {
        $period = $this->getPeriod();
        $data = [];

        foreach ($period as $date)
        {
            $totalFacturationForMonth = Facture::where('month', $date->month)
                ->where('year', $date->year)
                ->sum('montant_ttc');

            $data[] = [
                'date' => $date->shortMonthName . ' ' .$date->year,
                'count' => $totalFacturationForMonth
            ];
        }

        return $data;
    }

    /**
     * Permet de récupérer la période de l'année en cours
     * @return CarbonPeriod
     */
    private function getPeriod()
    {
        return CarbonPeriod::create(Carbon::now()->startOfYear(), '1 month', Carbon::now()->endOfMonth());
    }
}
