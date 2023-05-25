<?php

namespace App\Http\Livewire\Front\Invoice;

use App\Models\Facture;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceReservationDataTable extends Component
{
    use WithPagination;

    public Facture $facture;
    public int $perPage = 50;
    public string $search = "";

    public $queryString = [
        'search' => ['except' => '']
    ];

    public function mount(Facture $invoice): void
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
                ->orderBy('pickup_date')
                ->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }
}
