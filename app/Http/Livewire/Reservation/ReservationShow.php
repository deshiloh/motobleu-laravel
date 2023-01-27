<?php

namespace App\Http\Livewire\Reservation;

use _PHPStan_446ead745\Nette\Neon\Exception;
use App\Enum\ReservationStatus;
use App\Events\ReservationCanceled;
use App\Events\ReservationConfirmed;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Models\Pilote;
use App\Models\Reservation;
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
            'message' => 'required|string',
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
        $this->validate();

        try {
            $this->reservation->statut = ReservationStatus::Confirmed;

            $this->reservation->pilote()->associate(
                Pilote::findOrFail($this->reservation->pilote_id)
            );

            $this->reservation->update([
                'statut' => ReservationStatus::Confirmed,
            ]);

            \Mail::to($this->reservation->pilote->email)
                ->send(new PiloteAttached($this->reservation));

            ReservationConfirmed::dispatch($this->reservation);

            $this->notification()->success(
                title: "Opération réussite",
                description: "La réservation a bien été confirmée."
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur s'est produite",
                description: "Une erreur s'est produite pendant la confirmation de la réservation"
            );
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
        }

    }

    public function updatePilote()
    {
        $this->validate();

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
        }
    }

    public function cancelAction()
    {
        $this->reservation->statut = ReservationStatus::Canceled;

        $this->reservation->update([
            'statut' => ReservationStatus::Canceled
        ]);

        ReservationCanceled::dispatch($this->reservation);
    }

    public function cancelBilledAction()
    {
        $this->reservation->statut = ReservationStatus::CanceledToPay;

        $this->reservation->update([
            'statut' => ReservationStatus::CanceledToPay,
        ]);

        ReservationCanceled::dispatch($this->reservation);
    }
}
