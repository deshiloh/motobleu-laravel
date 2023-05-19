<?php

namespace App\Http\Livewire\Pilote;

use App\Enum\ReservationStatus;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
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
        $this->dateDebut = $this->dateDebut ?? Carbon::now("Europe/Paris")->startOfMonth()->addHours(3);
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
        $validator = Validator::make($datas, [
            'encaisse' => 'nullable',
            'encompte' => 'nullable',
            'comment' => 'nullable',
            'reservation' => 'required'
        ]);

        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $datas = $validator->getData();

            if (is_null($datas['encaisse']) && is_null($datas['encompte'])) {
                $validator->errors()->add(
                    'encompte', 'En compte et encaisse doivent être renseigné'
                );
                return false;
            }

            if ($datas['encaisse'] > 0 && $datas['encompte'] > 0) {
                $validator->errors()->add(
                    'encompte', 'Encompte et encaisse ne peuvent pas avoir de valeurs en même temps'
                );
                return false;
            }
        });

        if ($validator->fails()) {
            $description = implode('<br>', $validator->errors()->all());
            $this->notification()->error('Erreur', $description);
            return false;
        }

        $reservation = Reservation::find($datas['reservation']);
        $reservation->updateQuietly([
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
