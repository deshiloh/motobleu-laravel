<?php

namespace App\Http\Livewire\Pilote;

use App\Enum\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PiloteStatMonth extends Component
{
    use WithPagination;

    public array $months;
    public array $years;

    public ?string $selectedMonth = null;
    public ?string $selectedYear = null;
    public int $perPage = 200;

    protected $queryString = [
        'selectedMonth' => ['except' => null],
        'selectedYear' => ['except' => null],
    ];

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;

        $this->months = [
            ['id' => 1,  'valeur' => 'Janvier'],
            ['id' => 2,  'valeur' => 'Février'],
            ['id' => 3,  'valeur' => 'Mars'],
            ['id' => 4,  'valeur' => 'Avril'],
            ['id' => 5,  'valeur' => 'Mai'],
            ['id' => 6,  'valeur' => 'Juin'],
            ['id' => 7,  'valeur' => 'Juillet'],
            ['id' => 8,  'valeur' => 'Août'],
            ['id' => 9,  'valeur' => 'Septembre'],
            ['id' => 10, 'valeur' => 'Octobre'],
            ['id' => 11, 'valeur' => 'Novembre'],
            ['id' => 12, 'valeur' => 'Décembre'],
        ];

        $this->years = DB::table('reservations')
            ->select(DB::raw('YEAR(created_at) as year')) // Extraction de l'année de la date de création
            ->distinct() // Années uniques
            ->orderBy('year', 'desc') // Tri par ordre décroissant
            ->pluck('year')->toArray(); // Récupération des années dans une collection
    }

    public function render()
    {
        return view('livewire.pilote.pilote-stat-month', [
            'pilotes' => $this->getDatas()
        ])
            ->layout('components.layout');
    }

    private function getDatas()
    {
        return DB::table('pilotes')
            ->distinct()
            ->select(
                'pilotes.id',
                'pilotes.nom',
                'pilotes.prenom',
                DB::raw('SUM(reservations.encompte_pilote) as total_encompte'),
                DB::raw('SUM(reservations.encaisse_pilote + reservations.encompte_pilote) as chiffre_affaire'),
                DB::raw('SUM((reservations.encompte_pilote + reservations.encaisse_pilote) * (COALESCE(reservations.commission, pilotes.commission) / 100)) as total_commission'),
            )
            ->leftJoin('reservations', function ($join) {
                $join->on('pilotes.id', '=', 'reservations.pilote_id')
                    ->whereIn('reservations.statut', [
                        ReservationStatus::Confirmed->value,
                        ReservationStatus::Billed->value,
                        ReservationStatus::CanceledToPay
                    ])
                    ->whereMonth('reservations.pickup_date', $this->selectedMonth)
                    ->whereYear('reservations.pickup_date', $this->selectedYear);

            })
            ->having('total_encompte', '>', 0)
            ->groupBy('pilotes.id', 'pilotes.nom', 'pilotes.prenom')
            ->orderBy('chiffre_affaire', 'desc')
            ->paginate($this->perPage);
    }
}
