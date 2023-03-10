<?php

namespace App\Http\Livewire\Front\Invoice;

use App\Models\Facture;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class InvoiceDataTable extends Component
{
    public string $search = "";
    public int $perPage = 20;

    public function render()
    {
        $factures = Facture::query()
            ->select('factures.*')
            ->join('reservations', 'reservations.facture_id', '=', 'factures.id')
            ->join('entreprises', 'reservations.entreprise_id', '=', 'entreprises.id')
            ->whereIn('entreprises.id', \Auth::user()->entreprises()->pluck('id'))
            ->when($this->search, function (Builder $query, $search) {
                return $query->where('factures.reference', 'like' , '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.front.invoice.invoice-data-table', [
            'factures' => $factures
        ])
            ->layout('components.front-layout');
    }
}
