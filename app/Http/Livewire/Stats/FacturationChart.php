<?php

namespace App\Http\Livewire\Stats;

use App\Models\Entreprise;
use App\Models\Facture;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class FacturationChart extends Component
{
    public string $selectedYear = '';
    public ?string $selectedEntreprise = null;
    public array $dataset = [];
    public array $labels = [];

    protected $listeners = [
        'onChangeSelectedYear',
        'onChangeSelectedEntreprise'
    ];

    public function mount(): void
    {
        if ($this->selectedYear === '') {
            $this->selectedYear = (string) Carbon::now()->year;
        }

        $this->labels = $this->getLabels();

        $this->dataset = $this->getDataSets();
    }

    public function render()
    {
        return view('livewire.stats.facturation-chart');
    }

    /**
     * Listener quand l'année sélectionnée a changé
     * @param $newValue
     * @return void
     */
    public function onChangeSelectedYear($newValue): void
    {
        $this->selectedYear = $newValue;

        $labels = $this->getLabels();
        $dataset = $this->getDataSets();

        $this->emit('updateChart', [
            'datasets' => $dataset,
            'labels' => $labels,
        ]);
    }

    /**
     * Listener quand l'entreprise sélectionnée a changé
     * @param $newValue
     * @return void
     */
    public function onChangeSelectedEntreprise($newValue): void
    {
        $this->selectedEntreprise = $newValue;

        $labels = $this->getLabels();
        $dataset = $this->getDataSets();

        $this->emit('updateChart', [
            'datasets' => $dataset,
            'labels' => $labels,
        ]);
    }

    /**
     * Génération des labels pour le graphique
     * @return array
     */
    private function getLabels(): array
    {
        $period = $this->getPeriod();
        $labels = [];

        foreach ($period as $currentMonth) {
            $labels[] = $currentMonth->monthName;
        }

        return $labels;
    }

    /**
     * Génération des sets de données, un set correspond à une entreprise.
     * @return array
     */
    private function getDataSets(): array
    {
        $dataset = [];

        $entreprises = Entreprise::orderBy('nom')
            ->when($this->selectedEntreprise !== null, function (Builder $query) {
                $query->where('id', $this->selectedEntreprise);
            })
            ->where('is_actif', true)->get();

        foreach ($entreprises as $entreprise) {
            $dataset[] = [
                'label' => $entreprise->nom,
                'backgroundColor' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',255)',
                'borderColor' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',255)',
                'data' => $this->getFacturationInPeriodForEntreprise($entreprise),
                'tension' => 0.1
            ];
        }

        return $dataset;
    }

    /**
     * Récupère le nombre de réservations sur une période
     * @param Entreprise $entreprise
     * @return array
     */
    private function getFacturationInPeriodForEntreprise(Entreprise $entreprise): array
    {
        $period = $this->getPeriod();
        $datas = [];

        foreach ($period as $currentMonth) {
            $datas[] = Facture::whereHas('reservations', function (Builder $builder) use ($entreprise) {
                $builder->where('entreprise_id', $entreprise->id);
            })
                ->where('month', $currentMonth->month)
                ->where('year', $currentMonth->year)
                ->sum('montant_ttc');
        }

        return $datas;
    }

    /**
     * Génération de la période (ici 1 an)
     * @return CarbonPeriod
     */
    private function getPeriod(): CarbonPeriod
    {
        return CarbonPeriod::create(
            Carbon::create($this->selectedYear)->startOfYear(),
            '1 month',
            Carbon::create($this->selectedYear)->endOfYear()
        );
    }
}
