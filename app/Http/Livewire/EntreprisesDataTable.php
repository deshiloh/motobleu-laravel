<?php

namespace App\Http\Livewire;

use App\Models\Entreprise;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class EntreprisesDataTable extends Component
{
    use WithPagination, WithSorting;

    public $search = '';
    public $sortField = 'nom';
    public $perPage = 8;

    public function render()
    {
        return view('livewire.entreprises-data-table', [
            'entreprises' => Entreprise::search('nom', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
