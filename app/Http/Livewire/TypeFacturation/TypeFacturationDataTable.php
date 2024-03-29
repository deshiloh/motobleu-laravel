<?php

namespace App\Http\Livewire\TypeFacturation;

use App\Models\TypeFacturation;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class TypeFacturationDataTable extends Component
{
    use WithPagination, WithSorting, Actions;

    public string $search = '';
    public string $sortField = 'nom';
    public int $perPage = 10;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.type-facturation.type-facturation-data-table', [
            'typefacturations' => TypeFacturation::when($this->search, function (Builder $query, $search) {
                $query->where('nom', 'LIKE', '%' . $search . '%');
            })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function toggleEtatTypeFacturation(TypeFacturation $typeFacturation)
    {
        $typeFacturation->is_actif = !$typeFacturation->is_actif;

        $typeFacturation->update();

        $this->notification()->success(
            "Opération réussite",
            $typeFacturation->is_actif ?
                "Type facturation " . $typeFacturation->nom . " activé." :
                "Type facturation " . $typeFacturation->nom . " désactivé."
        );
    }
}
