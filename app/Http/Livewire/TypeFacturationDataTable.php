<?php

namespace App\Http\Livewire;

use App\Models\TypeFacturation;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class TypeFacturationDataTable extends Component
{
    use WithPagination, WithSorting;

    public $search;
    public $sortField = 'nom';
    public $perPage = 10;

    public function render()
    {
        return view('livewire.type-facturation-data-table', [
            'typefacturations' => TypeFacturation::search('nom', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
