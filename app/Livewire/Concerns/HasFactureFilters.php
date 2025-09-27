<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

trait HasFactureFilters
{
    use WithPagination;

    public ?int $entreprise = null;
    public int $perPage = 10;

    public function updatedEntreprise(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    protected function getEntrepriseOptions(): array
    {
        return [
            'label' => 'Entreprise',
            'placeholder' => 'Rechercher une entreprise',
            'async-data' => route('api.entreprises'),
            'option-label' => 'nom',
            'option-value' => 'id'
        ];
    }
}