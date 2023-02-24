<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AdressesEntrepriseDataTable extends Component
{
    public Entreprise $entreprise;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.entreprise.adresses-entreprise-data-table', [
            'adresses' => AdresseEntreprise::where('entreprise_id', $this->entreprise->id)->get()
        ]);
    }
}
