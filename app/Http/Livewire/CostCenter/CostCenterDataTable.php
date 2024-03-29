<?php

namespace App\Http\Livewire\CostCenter;

use App\Models\CostCenter;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class CostCenterDataTable extends Component
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
        return view('livewire.cost-center.cost-center-data-table', [
            'costcenters' => CostCenter::when($this->search, function (Builder $query, $search) {
                $query->where('nom', 'LIKE', '%' . $search . '%');
            })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function toggleStatutCostCenter(CostCenter $costCenter)
    {
        $costCenter->is_actif = !$costCenter->is_actif;
        $costCenter->update();

        $this->notification()->success(
            "Opération réussite",
            $costCenter->is_actif ?
                "Cost Center " . $costCenter->nom . " activé" :
                "Cost Center " . $costCenter->nom . " désactivé."
        );
    }
}
