<?php

namespace App\Http\Livewire;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AdressesEntrepriseDataTable extends Component
{
    use WithPagination;

    public int $perPage = 15;
    public Entreprise $entreprise;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.adresses-entreprise-data-table', [
            'adresses' => AdresseEntreprise::where('entreprise_id', $this->entreprise->id)
                ->paginate($this->perPage)
        ]);
    }
}
