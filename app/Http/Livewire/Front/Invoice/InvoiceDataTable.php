<?php

namespace App\Http\Livewire\Front\Invoice;

use App\Enum\BillStatut;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceDataTable extends Component
{
    use WithPagination;

    public string $search = "";
    public int $perPage = 20;

    public  $queryString = [
        'search' => ['exception' => '']
    ];

    public function render()
    {
        $factures = Facture::where('is_acquitte', true)
            ->when(\Auth::user()->is_admin_ardian, function (Builder $query) {
                $query->whereHas('entreprise', function(Builder $query) {
                    $query->whereIn('id', \Auth::user()->entreprises()->pluck('id')->toArray());
                });
            }, function(Builder $query) {
                $query->whereHas('reservations.passager.user', function(Builder $query) {
                    $query->where('id', \Auth::user()->id);
                });
            })
            ->when($this->search, function (Builder $query) {
                $query->where('reference', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.front.invoice.invoice-data-table', [
            'factures' => $factures
        ])
            ->layout('components.front-layout');
    }
}
