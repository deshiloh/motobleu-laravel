<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Models\Facture;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class FacturationChart extends Component
{
    public array $dataset = [];
    public Entreprise $entreprise;

    public function mount(Entreprise $entreprise)
    {
        $this->entreprise = $entreprise;
        $this->dataset = $this->getDatas();
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.entreprise.facturation-chart');
    }

    private function getDatas(): array
    {
        $period = now()->subMonths(6)->monthsUntil(now());
        $data = [];

        foreach ($period as $date)
        {

            $totalFacturationForMonth = Facture::whereHas('reservations', function (Builder $builder) {
                $builder->where('entreprise_id', $this->entreprise->id);
            })
                ->where('month', $date->month)
                ->where('year', $date->year)
                ->sum('montant_ht');

            $data[] = [
                'date' => $date->shortMonthName . ' ' .$date->year,
                'count' => $totalFacturationForMonth
            ];
        }

        return $data;
    }
}
