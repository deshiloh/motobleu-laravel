<?php

namespace App\Http\Livewire;

use App\Models\Localisation;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class LocalisationDataTable extends Component
{
    use WithPagination, WithSorting;

    public $perPage = 10;
    public $sortField = 'nom';
    public $search;

    public function render()
    {
        return view('livewire.localisation-data-table', [
            'localisations' => Localisation::search('nom', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
