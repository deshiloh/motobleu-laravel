<?php

namespace App\Http\Livewire\CostCenter;

use App\Models\CostCenter;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CostCenterDataTable extends Component
{
    use WithPagination, WithSorting;

    public string $search = '';
    public string $sortField = 'nom';
    public int $perPage = 20;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.cost-center.cost-center-data-table', [
            'costcenters' => CostCenter::search($this->search)->query(function ($q) {
                $q->orderBy($this->sortField, $this->sortDirection);
            })->paginate($this->perPage)
        ]);
    }
}
