<?php

namespace App\Http\Livewire\Localisation;

use App\Models\Localisation;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class LocalisationDataTable extends Component
{
    use WithPagination, WithSorting, Actions;

    public int $perPage = 20;
    public string $sortField = 'nom';
    public string $search = '';

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.localisation.localisation-data-table', [
            'localisations' => Localisation::when($this->search, function (Builder $query, $search) {
                $query->where('nom', 'LIKE', '%' . $search . '%');
            })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function toggleStatus(Localisation $localisation)
    {
        $localisation->is_actif = !$localisation->is_actif;
        $localisation->update();

        $this->notification()->success(
            "Opération réussite",
            $localisation->is_actif ? "Localisation activée." : "Localisation désactivée"
        );
    }
}
