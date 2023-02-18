<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Models\Reservation;
use App\Services\ExportService;
use Google\Service\Vault\Export;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Exception;
use WireUi\Traits\Actions;

class RecapReservationEntreprise extends Component
{
    use WithPagination, Actions;

    public Entreprise $entreprise;
    public $dateDebut;
    public $dateFin;
    public int $perPage = 10;
    protected $queryString = ['dateDebut', 'dateFin'];

    public function render(): Factory|View|Application
    {
        return view('livewire.entreprise.recap-reservation-entreprise', [
            'reservations' => Reservation::where('entreprise_id', $this->entreprise->id)
            ->when($this->dateDebut && $this->dateFin, function(Builder $query) {
                return $query
                    ->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin]);
            })
                ->orderBy('id', 'desc')
            ->paginate($this->perPage, ['*'], 'reservationsPage')
        ]);
    }
}
