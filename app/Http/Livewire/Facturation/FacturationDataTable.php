<?php

namespace App\Http\Livewire\Facturation;

use App\Models\Entreprise;
use App\Models\Facture;
use Livewire\Component;

class FacturationDataTable extends Component
{
    public string $search = '';
    public Entreprise $entreprise;

    public function mount()
    {

    }

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function render()
    {
        return view('livewire.facturation.facturation-data-table', [
            'facturations' => Facture::paginate(10)
        ])
            ->layout('components.admin-layout');
    }
}
