<?php

namespace App\Http\Livewire\Facturation;

use App\Models\Entreprise;
use Livewire\Component;

class EditionFacture extends Component
{
    public $entrepriseId = 0;

    public function getEntrepriseProperty()
    {
        return !empty($this->entrepriseId) ? Entreprise::find($this->entrepriseId) : null;
    }

    protected function getRules()
    {
        return [
            'entrepriseId' => 'required'
        ];
    }

    public function render()
    {
        return view('livewire.facturation.edition-facture')
            ->layout('components.admin-layout');
    }
}
