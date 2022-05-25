<?php

namespace App\Http\Livewire;

use App\Models\Passager;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class PassagersDataTable extends Component
{
    use WithPagination, WithSorting;

    public $search;
    public $sortField = 'nom';
    public $perPage = 10;

    public function render()
    {
        return view('livewire.passagers-data-table', [
            'passagers' => Passager::search('nom', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
