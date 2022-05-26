<?php

namespace App\Http\Livewire;

use App\Models\Pilote;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class PiloteDataTable extends Component
{
    use WithPagination, WithSorting;

    public $search;
    public $sortField = 'nom';
    public $perPage = 10;

    public function render()
    {
        return view('livewire.pilote-data-table', [
            'pilotes' => Pilote::search('nom', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
