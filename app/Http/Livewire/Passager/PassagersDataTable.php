<?php

namespace App\Http\Livewire\Passager;

use App\Models\Passager;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class PassagersDataTable extends Component
{
    use WithPagination, WithSorting, Actions;

    public string $search = '';
    public string $sortField = 'nom';
    public int $perPage = 20;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.passager.passagers-data-table', [
            'passagers' => Passager::when($this->search, function (Builder $query, $search) {
                $query->where('nom', 'LIKE', '%'.$search.'%');
            })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    /**
     * Désactive un passager sélectionné.
     * @param Passager $passager
     * @return void
     */
    public function disablePassenger(Passager $passager): void
    {
        $passager->is_actif = false;
        $passager->update();

        $this->notification()->success(
            "Opération réussite",
            "Le passager " . $passager->nom . " a bien été désactivé."
        );
    }

    /**
     * Active un passager sélectionné.
     * @param Passager $passager
     * @return void
     */
    public function enablePassenger(Passager $passager): void
    {
        $passager->is_actif = true;
        $passager->update();

        $this->notification()->success(
            "Opération réussite",
            "Le passager " . $passager->nom . " a bien été activé."
        );
    }
}
