<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Models\Reservation;
use App\Services\ExportService;
use Google\Service\Vault\Export;
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

    public function render()
    {
        return view('livewire.entreprise.recap-reservation-entreprise', [
            'reservations' => $this->buildQuery()
        ]);

        /*
        $reservations = Reservation::query()
            ->join('passagers', 'reservations.passager_id', '=', 'passagers.id')
            ->join('users', 'passagers.user_id', '=', 'users.id')
            ->join('entreprises', 'users.entreprise_id', '=', 'entreprises.id')
            ->where('entreprises.id', "=", $this->entreprise->id);

        if ($this->dateDebut && $this->dateFin) {
            $reservations->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin]);
        }

        return view('livewire.entreprise.recap-reservation-entreprise', [
            'reservations' => $reservations->paginate($this->perPage)
        ]);*/
    }

    public function buildQuery()
    {
        $query = Reservation::where('entreprise_id', $this->entreprise->id);

        if ($this->dateDebut && $this->dateFin) {
            $query
                ->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin]);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportReservations()
    {
        $exportService = \App::make(ExportService::class);

        try {
            return $exportService->exportReservations(2022, 12, $this->entreprise);
        } catch (\Exception $exception) {
            ray()->exception($exception);
            $this->notification()->error(
                title: 'Erreur',
                description: "Une erreur est survenue."
            );
        }
    }
}
