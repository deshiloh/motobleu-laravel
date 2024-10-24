<?php

namespace App\Http\Livewire\Reservation;

use App\Enum\ReservationStatus;
use App\Events\ReservationCanceled;
use App\Events\ReservationCanceledPay;
use App\Events\ReservationConfirmed;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Models\Pilote;
use App\Models\Reservation;
use Illuminate\Validation\Validator;
use Livewire\Component;
use WireUi\Traits\Actions;

class ReservationShow extends Component
{
    use Actions;

    public Reservation $reservation;
    public string $message;
    public ?float $resaComm = null;

    public function mount(Reservation $reservation)
    {
        $this->reservation = $reservation;

        $this->message = "Bonjour,
Votre réservation a bien été prise en compte";

        if (is_null($reservation->pilote_id)) {
            $defaultPilote = Pilote::firstWhere('email', 'pilotes.motobleu@gmail.com');
            $reservation->pilote_id = $defaultPilote->id;
        }

        if (is_null($this->reservation->commission)) {
            $selectedPilote = Pilote::find($reservation->pilote_id);
            $this->resaComm = $selectedPilote->commission;
        } else {
            $this->resaComm = $this->reservation->commission;
        }
    }

    /**
     * Event Livewire quand la réservation est modifiée.
     * @return void
     */
    public function updatedReservation(): void
    {
        $selectedPilote = Pilote::find($this->reservation->pilote_id);
        $this->resaComm = $selectedPilote->commission;
    }

    public function render()
    {
        return view('livewire.reservation.reservation-show')
            ->layout('components.layout');
    }

    protected function getRules(): array
    {
        return [
            'reservation.pilote_id' => 'required',
            'reservation.send_to_passager' => 'boolean',
            'reservation.calendar_passager_invitation' => 'boolean',
            'message' => 'nullable|string',
            'reservation.encaisse_pilote' => 'nullable|numeric',
            'reservation.encompte_pilote' => 'nullable|numeric',
            'reservation.comment_pilote' => 'nullable',
            'resaComm' => 'nullable|numeric',
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'reservation.pilote_id' => 'pilote'
        ];
    }

    public function confirmedAction()
    {
        $this->handleConfirmedAndUpdateValidation();

        try {
            $this->reservation->statut = ReservationStatus::Confirmed;

            $this->reservation->encaisse_pilote = (float) $this->reservation->encaisse_pilote;
            $this->reservation->encompte_pilote = (float) $this->reservation->encompte_pilote;

            $this->reservation->commission = $this->resaComm;

            $this->reservation->update();

            $this->reservation->refresh();

            \Mail::to($this->reservation->pilote->email)
                ->send(new PiloteAttached($this->reservation));

            ReservationConfirmed::dispatch($this->reservation, $this->message);

            $this->notification()->success(
                title: "Opération réussite",
                description: "La réservation a bien été confirmée."
            );

            return redirect()->to(route('admin.reservations.index'));
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur s'est produite",
                description: "Une erreur s'est produite pendant la confirmation de la réservation"
            );
            if (\App::environment(['local'])) {
                ray($this->reservation);
                ray()->exception($exception);
            }
            if(\App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant l'ajout / modification pilote d'une réservation", [
                    'exception' => $exception,
                    'reservation' => $this->reservation
                ]);
            }
        }

    }

    public function updatePilote()
    {
        $this->handleConfirmedAndUpdateValidation();

        $this->reservation->commission = $this->resaComm;
        $this->reservation->encaisse_pilote = (float) $this->reservation->encaisse_pilote;
        $this->reservation->encompte_pilote = (float) $this->reservation->encompte_pilote;

        if ($this->reservation->isDirty(['pilote_id'])) {
            /** @var Pilote $currentPilote */
            $currentPilote = Pilote::findOrFail($this->reservation->getOriginal('pilote_id'));
            /** @var Pilote $newPilote */
            $newPilote = Pilote::findOrFail($this->reservation->pilote_id);

            $this->reservation->pilote()->associate($newPilote);
            $this->reservation->update();

            \Mail::to($currentPilote->email)->send(new PiloteDetached($this->reservation));
            \Mail::to($newPilote->email)->send(new PiloteAttached($this->reservation));

            $this->notification()->success(
                title: "Opération réussite",
                description: 'Pilote correctement modifié.'
            );

            return redirect()->to(route('admin.reservations.index'));
        }

        if ($this->reservation->isDirty([
            'encompte_pilote',
            'encaisse_pilote',
            'comment_pilote',
            'commission'
        ])) {
            $this->reservation->update();

            return redirect()->to(route('admin.reservations.index'));
        }
    }

    public function cancelAskAction()
    {
        $this->dialog()->confirm([
            'title'       => 'Attention !',
            'description' => 'Êtes vous sûr de vouloir annuler la réservation ?',
            'icon'        => 'question',
            'accept'      => [
                'label'  => 'Oui',
                'method' => 'cancelAction'
            ],
            'reject' => [
                'label'  => 'Non',
            ],
        ]);
    }

    public function cancelToPayAskAction()
    {
        $this->dialog()->confirm([
            'title'       => 'Attention !',
            'description' => 'Êtes vous sûr de vouloir annuler la réservation ? Celle ci sera cependant facturable.',
            'icon'        => 'question',
            'accept'      => [
                'label'  => 'Oui',
                'method' => 'cancelBilledAction'
            ],
            'reject' => [
                'label'  => 'Non',
            ],
        ]);
    }

    public function cancelBilledAction()
    {
        $this->reservation->statut = ReservationStatus::CanceledToPay;

        $this->reservation->update([
            'statut' => ReservationStatus::CanceledToPay,
        ]);

        ReservationCanceledPay::dispatch($this->reservation);

        return redirect()->to(route('admin.reservations.index'));
    }

    public function cancelAction()
    {
        $this->reservation->statut = ReservationStatus::Canceled;

        $facture = $this->reservation->facture;

        if ($facture !== null) {
            if ($facture->reservations->count() > 1) {
                $this->reservation->tarif = null;
                $this->reservation->majoration = null;
                $this->reservation->complement = null;

                $this->reservation->update([
                    'statut' => ReservationStatus::Canceled,
                    'facture_id' => null,
                    'tarif' => null,
                    'majoration' => null,
                    'complement' => null
                ]);
            } else {
                $this->reservation->tarif = 0;
                $this->reservation->majoration = 0;
                $this->reservation->complement = 0;

                $facture->montant_ttc = 0;

                $this->reservation->update([
                    'statut' => ReservationStatus::Billed,
                    'tarif' => 0,
                    'majoration' => 0,
                    'complement' => 0
                ]);

                $facture->refresh();
            }
        } else {
            $this->reservation->update([
                'statut' => ReservationStatus::Canceled
            ]);
        }

        ReservationCanceled::dispatch($this->reservation);

        return redirect()->to(route('admin.reservations.index'));
    }

    public function confirmedStatusAction(): void
    {
        $this->reservation->statut = ReservationStatus::Confirmed;

        $this->reservation->update([
            'statut' => ReservationStatus::Confirmed,
        ]);
    }

    private function handleConfirmedAndUpdateValidation(): void
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {

                if ($this->reservation->encaisse_pilote > 0 && $this->reservation->encompte_pilote > 0) {
                    $validator
                        ->errors()->add(
                            'reservation.encaisse_pilote', 'Encaisse et en compte ne peuvent être renseignée en
                            même temps.'
                        );
                    $validator->errors()
                        ->add('reservation.encompte_pilote', 'Encaisse et en compte ne peuvent être renseignée en
                            même temps.');
                    return false;
                }

                return true;
            });
        })->validate();
    }
}
