<?php

namespace App\Http\Livewire;

use App\Models\CostCenter;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class CostCenterDataTable extends Component
{
    use WithPagination, WithSorting;

    public $search;
    public $sortField = 'nom';
    public $perPage = 10;

    public function render()
    {
        return view('livewire.cost-center-data-table', [
            'costcenters' => CostCenter::search('nom', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
