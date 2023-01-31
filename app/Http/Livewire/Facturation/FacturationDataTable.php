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
    public ?int $entreprise = null;
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'entreprise' => ['except' => null]
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
        return Facture::where('factures.reference', 'like', '%' . $this->search . '%')
            ->when($this->entreprise != 0, function (Builder $query) {
                return $query
                    ->join('reservations', 'reservations.facture_id', '=', 'factures.id')
                    ->where('reservations.entreprise_id', $this->entreprise);
            })
            ->orderBy('factures.id', 'desc')
            ->paginate($this->perPage);
    }
}
