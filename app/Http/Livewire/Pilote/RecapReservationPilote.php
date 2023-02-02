<?php

namespace App\Http\Livewire\Pilote;

use App\Enum\ReservationStatus;
use App\Exports\ReservationPiloteExport;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use WireUi\Traits\Actions;

class RecapReservationPilote extends Component
{
    use WithPagination, Actions;

    public Pilote $pilote;
    public $reservations = [];
    public $dateDebut;
    public $dateFin;
    protected $queryString = ['dateDebut', 'dateFin'];
    protected $listeners = [
        'editReservation'
    ];

    /**
     * @param Pilote $pilote
     * @return void
     */
    public function mount(Pilote $pilote): void
    {
        $this->pilote = $pilote;
        $this->dateDebut = $this->dateDebut ?? Carbon::today()->startOfMonth()->addHour();
        $this->dateFin = $this->dateFin ?? Carbon::today()->endOfMonth();
        $this->reservations = $this->handleQuery();
        $this->reservationSelected = null;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.pilote.recap-reservation-pilote')
            ->layout('components.layout');
    }

    private function handleQuery()
    {
        return Reservation::where('pilote_id', $this->pilote->id)
            ->where('statut', ReservationStatus::Confirmed->value)
            ->whereBetween('pickup_date', [$this->dateDebut, $this->dateFin])
            ->orderBy('pickup_date', 'desc')
            ->get();
    }

    public function searchReservations()
    {
        $this->reservations = $this->handleQuery();
    }

    public function editReservation(array $datas): bool
    {
        $validator = \Validator::make($datas, [
            'tarif' => 'required',
            'majoration' => 'nullable',
            'encaisse' => 'nullable',
            'encompte' => 'nullable',
            'comment' => 'nullable',
            'reservation' => 'required'
        ]);

        if ($validator->fails()) {
            $description = implode('<br>', $validator->errors()->all());
            $this->notification()->error('Erreur', $description);
            return false;
        }

        $reservation = Reservation::find($datas['reservation']);
        $reservation->updateQuietly([
            'tarif_pilote' => $datas['tarif'],
            'majoration_pilote' => $datas['majoration'],
            'encaisse_pilote' => $datas['encaisse'],
            'encompte_pilote' => $datas['encompte'],
            'comment_pilote' => $datas['comment'],
        ]);

        $this->reservations = $this->handleQuery();

        $this->notification()->success('Opération réussite', "La réservation a bien été modifiée.");

        return true;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportReservations(ExportService $exportService): BinaryFileResponse
    {
        return $exportService->exportForPilote([$this->dateDebut, $this->dateFin], $this->pilote);
    }
}
