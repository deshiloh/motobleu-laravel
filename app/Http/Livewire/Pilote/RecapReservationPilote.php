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
        $validator = Validator::make($datas, [
            'tarif' => 'required',
            'majoration' => 'nullable',
            'encaisse' => 'nullable',
            'encompte' => 'nullable',
            'comment' => 'nullable',
            'reservation' => 'required'
        ]);

        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $datas = $validator->getData();
            if ($datas['encaisse'] > 0 && $datas['encompte'] > 0) {
                $validator->errors()->add(
                    'encompte', 'Encompte et encaisse ne peuvent pas avoir de valeurs en même temps'
                );
                return false;
            }

            $totalWithEncaisse = $datas['majoration'] + $datas['encaisse'];
            $totalWithEncompte = $datas['majoration'] + $datas['encompte'];

            if ($datas['encaisse'] > 0 && $datas['tarif'] != $totalWithEncaisse) {
                $validator->errors()->add(
                    'encaisse', 'Le total encaisse et majoration ne corresponds pas au tarif'
                );

                return false;
            }

            if ($datas['encompte'] > 0 && $datas['tarif'] != $totalWithEncompte) {
                $validator->errors()->add(
                    'encaisse', 'Le total en compte et majoration ne corresponds pas au tarif'
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
