<?php

namespace App\Http\Livewire;

use App\Models\AdresseEntreprise;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class AdressesEntrepriseDataTable extends Component
{
    use WithPagination;

    public $perPage = 15;
    public $entreprise;

    public function render()
    {
        return view('livewire.adresses-entreprise-data-table', [
            'adresses' => AdresseEntreprise::where('entreprise_id', $this->entreprise->id)
                ->paginate($this->perPage)
        ]);
    }
}
