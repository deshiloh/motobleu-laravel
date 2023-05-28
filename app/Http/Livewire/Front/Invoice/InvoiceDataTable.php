<?php

namespace App\Http\Livewire\Front\Invoice;

use App\Models\Facture;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceDataTable extends Component
{
    use WithPagination;

    public string $search = "";
    public int $perPage = 20;

    public function render()
    {
        $factures = Facture::whereHas('reservations', function(Builder $query) {
            $query->whereHas('entreprise', function(Builder $query) {
                $query->whereIn('id', \Auth::user()->entreprises()->pluck('id')->toArray());
            });
        })
            ->when($this->search, function (Builder $query) {
                $query->where('reference', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at')
            ->paginate($this->perPage);

        return view('livewire.front.invoice.invoice-data-table', [
            'factures' => $factures
        ])
            ->layout('components.front-layout');
    }
}
