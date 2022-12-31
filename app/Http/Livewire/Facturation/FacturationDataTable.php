<?php

namespace App\Http\Livewire\Facturation;

use App\Models\Entreprise;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithPagination;

class FacturationDataTable extends Component
{
    use WithPagination;

    public string $search = '';
    public int|null $entreprise = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'entreprise'
    ];

    public function render()
    {
        return view('livewire.facturation.facturation-data-table', [
            'facturations' => $this->buildQuery()
        ])
            ->layout('components.layout');
    }

    public function getEntreprise(Facture $facture): Model|Builder|null
    {
        return Entreprise::query()
            ->select('entreprises.*')
            ->join('entreprise_user', 'entreprise_user.entreprise_id', '=', 'entreprises.id')
            ->join('reservations', 'reservations.entreprise_id', '=', 'entreprise_user.entreprise_id')
            ->where('reservations.facture_id', $facture->id)
            ->first();
    }

    public function buildQuery()
    {
        $factures = Facture::where('reference', 'like', '%' . $this->search . '%');

        if ($this->entreprise != 0) {
            $factures->whereHas('reservations', function (Builder $query) {
                $query->where('entreprise_id', $this->entreprise);
            });
        }

        return $factures->paginate(10);
    }
}
