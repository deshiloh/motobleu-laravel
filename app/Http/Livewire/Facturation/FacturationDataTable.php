<?php

namespace App\Http\Livewire\Facturation;

use App\Models\Entreprise;
use App\Models\Facture;
use Livewire\Component;

class FacturationDataTable extends Component
{
    public string $search = '';
    public int|null $entreprise = 0;

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function render()
    {
        return view('livewire.facturation.facturation-data-table', [
            'facturations' => $this->buildQuery()
        ])
            ->layout('components.layout');
    }

    public function getEntreprise(Facture $facture)
    {
        return Entreprise::query()
            ->select('entreprises.*')
            ->join('users', 'entreprises.id', '=', 'users.entreprise_id')
            ->join('passagers', 'passagers.user_id', '=', 'users.id')
            ->join('reservations', 'reservations.passager_id', '=', 'passagers.id')
            ->where('reservations.facture_id', $facture->id)
            ->first();
    }

    public function buildQuery()
    {
        $factures = Facture::query()
            ->select('factures.*');
        if ($this->entreprise) {
            $factures = $factures->join('reservations', 'factures.id', '=', 'reservations.facture_id')
                ->join('passagers', 'reservations.passager_id', '=', 'passagers.id')
                ->join('users', 'users.id', '=', 'passagers.user_id')
                ->join('entreprises', 'users.entreprise_id', '=', 'entreprises.id')
                ->where('entreprises.id', $this->entreprise)
                ->groupBy('factures.id');
        }

        if ($this->search) {
            $factures = $factures->where('factures.reference', 'like', '%' . $this->search . '%');
        }

        $factures = $factures->paginate(10);

        return $factures;
    }
}
