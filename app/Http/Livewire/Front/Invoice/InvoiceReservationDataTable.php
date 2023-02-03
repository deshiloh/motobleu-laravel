<?php

namespace App\Http\Livewire\Front\Invoice;

use App\Models\Facture;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class InvoiceReservationDataTable extends Component
{
    public Facture $facture;
    public int $perPage = 10;
    public string $search = "";

    public function mount(Facture $invoice)
    {
        $this->facture = $invoice;
    }
    public function render()
    {
        return view('livewire.front.invoice.invoice-reservation-data-table', [
            'reservations' => $this->facture->reservations()
                ->when($this->search, function (Builder $query, $search) {
                    $query->where('reference', 'like', '%' . $search . '%');
                })
                ->orderBy('pickup_date', 'desc')
                ->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }
}
