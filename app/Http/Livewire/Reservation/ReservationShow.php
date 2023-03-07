<?php

namespace App\Http\Livewire\Reservation;

use _PHPStan_446ead745\Nette\Neon\Exception;
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

    public function mount(Reservation $reservation)
    {
        $this->reservation = $reservation;

        $this->message = "Bonjour,
Votre réservation a bien été prise en compte
Cordialement.";

        if (is_null($reservation->pilote_id)) {
            $defaultPilote = Pilote::firstWhere('email', 'pilotes.motobleu@gmail.com');
            $reservation->pilote_id = $defaultPilote->id;
        }
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
            'reservation.send_to_user' => 'boolean',
            'reservation.send_to_passager' => 'boolean',
            'reservation.calendar_passager_invitation' => 'boolean',
            'reservation.calendar_user_invitation' => 'boolean',
            'message' => 'nullable|string',
            'reservation.encaisse_pilote' => 'nullable|numeric',
            'reservation.encompte_pilote' => 'nullable|numeric',
            'reservation.comment_pilote' => 'nullable',
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

            $this->reservation->update();

            $this->reservation->refresh();

            \Mail::to($this->reservation->pilote->email)
                ->send(new PiloteAttached($this->reservation));

            ReservationConfirmed::dispatch($this->reservation, $this->message);

            $this->notification()->success(
                title: "Opération réussite",
                description: "La réservation a bien été confirmée."
            );

            return redirect()->to(route('admin.homepage'));
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

            return redirect()->to(route('admin.homepage'));
        }

        if ($this->reservation->isDirty([
            'encompte_pilote',
            'encaisse_pilote',
            'comment_pilote'
        ])) {
            $this->reservation->update();

            return redirect()->to(route('admin.homepage'));
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
    }

    public function cancelAction()
    {
        $this->reservation->statut = ReservationStatus::Canceled;

        $this->reservation->update([
            'statut' => ReservationStatus::Canceled
        ]);

        ReservationCanceled::dispatch($this->reservation);
    }

    public function confirmedStatusAction()
    {
        $this->reservation->statut = ReservationStatus::Confirmed;

        $this->reservation->update([
            'statut' => ReservationStatus::Confirmed,
        ]);
    }

    private function handleConfirmedAndUpdateValidation()
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
            });
        })->validate();
    }
}
