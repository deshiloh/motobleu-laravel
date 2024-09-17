<?php

namespace App\Http\Livewire\Pilote;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PiloteStatYear extends Component
{
    use withPagination;

    public ?int $selectedYear = null;
    public array $years = [];

    protected $queryString = [
        'selectedYear' => ['except' => null],
    ];

    public int $perPage = 20;

    public function mount()
    {
        $this->selectedYear = Carbon::now()->year;

        $this->years = DB::table('reservations')
            ->select(DB::raw('YEAR(created_at) as year')) // Extraction de l'année de la date de création
            ->distinct() // Années uniques
            ->orderBy('year', 'desc') // Tri par ordre décroissant
            ->pluck('year')->toArray(); // Récupération des années dans une collection
    }

    public function render()
    {
        $pilotes = DB::table("pilotes")
            ->leftJoin('reservations', function ($join) {
                $join->on('pilotes.id', '=', 'reservations.pilote_id')
                    ->whereYear('reservations.pickup_date', $this->selectedYear);

            })
            ->select(
                'pilotes.id',
                'pilotes.nom',
                'pilotes.prenom',
                DB::raw('SUM(reservations.encompte_pilote) as total_encompte'),
                DB::raw('SUM(reservations.encaisse_pilote) as total_encaisse')
            )
            ->having('total_encompte', '>', 0)
            ->groupBy('pilotes.id', 'pilotes.nom', 'pilotes.prenom')
            ->orderBy('pilotes.nom')
            ->paginate($this->perPage);

        return view('livewire.pilote.pilote-stat-year', compact('pilotes'))
            ->layout('components.layout');
    }
}
