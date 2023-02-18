<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class EntreprisesDataTable extends Component
{
    use WithPagination, WithSorting, Actions;

    public string $search = '';
    public string $sortField = 'nom';
    public int $perPage = 8;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.entreprise.entreprises-data-table', [
            'entreprises' => Entreprise::query()
                ->when($this->search, function (Builder $query, $search) {
                    return $query->where('nom', 'like', '%' . $search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function disableEntreprise(Entreprise $entreprise)
    {
        $entreprise->is_actif = false;
        $entreprise->update();

        $this->notification()->success(
            'Opération réussite',
            'Entreprise ' .$entreprise->nom . ' désactivée'
        );
    }

    public function enableEntreprise(Entreprise $entreprise)
    {
        $entreprise->is_actif = true;
        $entreprise->update();

        $this->notification()->success(
            'Opération réussite',
            'Entreprise ' .$entreprise->nom . ' activée'
        );
    }
}
