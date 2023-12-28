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
use Symfony\Component\HttpFoundation\StreamedResponse;
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
            ->whereIn('statut', [
                ReservationStatus::CanceledToPay->value,
                ReservationStatus::Confirmed->value,
                ReservationStatus::Billed->value
            ])
            ->whereBetween('pickup_date', [$this->dateDebut . ' 00:00:00', $this->dateFin . ' 23:59:59'])
            ->orderBy('pickup_date')
            ->get();
    }

    public function searchReservations(): void
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
        $reservation->update([
            'encaisse_pilote' => (float) $datas['encaisse'],
            'encompte_pilote' => empty($datas['encompte']) ? 0 : (float) $datas['encompte'],
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
        if (!$this->dateDebut instanceof Carbon) {
            $this->dateDebut = Carbon::createFromFormat("Y-m-d", $this->dateDebut);
        }

        if (!$this->dateFin instanceof Carbon) {
            $this->dateFin = Carbon::createFromFormat("Y-m-d", $this->dateFin);
        }

        return $exportService->exportForPilote([$this->dateDebut, $this->dateFin], $this->pilote);
    }

    /**
     * Export du récap des courses pilotes en PDF
     * @param ExportService $exportService
     * @return StreamedResponse
     */
    public function exportRecapPdf(ExportService $exportService): StreamedResponse
    {
        if (!$this->dateDebut instanceof Carbon) {
            $this->dateDebut = Carbon::createFromFormat("Y-m-d", $this->dateDebut);
        }

        if (!$this->dateFin instanceof Carbon) {
            $this->dateFin = Carbon::createFromFormat("Y-m-d", $this->dateFin);
        }

        $pdfNameFile = sprintf(
            '%s_%s_%s.pdf',
            $this->pilote->nom,
            $this->dateDebut->format('m'),
            $this->dateDebut->format('Y')
        );

        return response()->streamDownload(function () use ($exportService) {
            echo $exportService->exportRecapForPilote(
                [$this->dateDebut, $this->dateFin],
                $this->pilote
            )->output();
        }, $pdfNameFile);
    }
}
