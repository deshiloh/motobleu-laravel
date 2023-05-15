<?php

namespace App\Http\Livewire\Facturation;

use App\Enum\BillStatut;
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
    public ?int $isAcquitte = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'isAcquitte' => ['except' => 0],
        'entreprise' => ['except' => null]
    ];

    public function render()
    {
        return view('livewire.facturation.facturation-data-table', [
            'facturations' => Facture::has('reservations')
                ->when($this->search, function (Builder $query) {
                    return $query->where('reference', 'like', '%'.$this->search.'%');
                })
                ->when($this->entreprise, function (Builder $query){
                    return $query->whereHas('reservations', function (Builder $query) {
                        return $query->where('entreprise_id', $this->entreprise);
                    });
                })
                ->when($this->isAcquitte > 0, function (Builder $query) {
                    return match ($this->isAcquitte) {
                        1 => $query->where('is_acquitte', false),
                        2 => $query->where('is_acquitte', true)
                    };
                })
                ->where('statut', BillStatut::COMPLETED->value)
                ->orderBy('factures.id', 'desc')
                ->paginate($this->perPage)
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
}
