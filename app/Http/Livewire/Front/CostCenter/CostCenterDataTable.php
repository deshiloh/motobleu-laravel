<?php

namespace App\Http\Livewire\Front\CostCenter;

use App\Models\CostCenter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class CostCenterDataTable extends Component
{
    public string $search = "";
    public int $perPage = 20;

    public function render()
    {
        return view('livewire.front.cost-center.cost-center-data-table', [
            'items' => CostCenter::when($this->search, function (Builder $query, $search) {
                $query->where('nom', 'like', '%' . $search . '%');
            })->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }

    public function toggleActifCostCenter(CostCenter $center)
    {
        $center->update([
            'is_actif' => !$center->is_actif
        ]);
    }
}
