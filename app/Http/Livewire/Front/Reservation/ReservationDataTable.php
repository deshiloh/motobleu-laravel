<?php

namespace App\Http\Livewire\Front\Reservation;

use App\Mail\CancelReservationDemand;
use App\Mail\UpdateReservationDemand;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class ReservationDataTable extends Component
{
    use WithPagination, Actions;

    public int $perPage = 20;
    public bool $editAskCard = false;
    public bool $askCancelCard = false;
    public ?string $message = null;
    public ?Reservation $selectedReservation;
    public string $search = '';

    protected array $rules = [
        'message' => 'required',
        'selectedReservation' => 'required'
    ];

    public function mount()
    {
        $this->selectedReservation = new Reservation();
    }

    public function render()
    {
        return view('livewire.front.reservation.reservation-data-table', [
            'reservations' => Reservation::where('reference', 'like', $this->search . '%')
                ->when(!\Auth::user()->is_admin_ardian, function(Builder $query) {
                    $query->whereHas('passager.user', function (Builder $query) {
                        $query->where('id', \Auth::user()->id);
                    });
                })
                ->whereIn('entreprise_id', \Auth::user()->entreprises()->pluck('id')->toArray())
                ->orderBy('pickup_date', 'desc')
                ->paginate($this->perPage)
        ])->layout('components.front-layout');
    }

    public function openAskEditModal(Reservation $reservation)
    {
        $this->selectedReservation = $reservation;
        $this->editAskCard = true;
    }

    /**
     * Permet d'envoyer un email de demande pour modifier la réservation
     * @return void
     */
    public function sendUpdateReservationEmail(): void
    {
        $this->validate();

        try {
            Mail::to(config('mail.admin.address'))
                ->send(
                    new UpdateReservationDemand($this->selectedReservation, $this->message)
                );

            $this->closeModal();

            $this->notification()->success(
                $title = 'Demande envoyé',
                $description = 'Votre demande a bien été envoyé et sera traité dans les plus brefs délais'
            );
        } catch (\Exception $exception) {
            $this->handleError($exception);
        }
    }

    public function sendCancelReservationEmail()
    {
        try {
            if (is_null($this->selectedReservation)) {
                throw new \Exception("Erreur pendant l'envoi de l'annulation");
            }
            Mail::to(config('mail.admin.address'))
                ->send(new CancelReservationDemand($this->selectedReservation, \Auth::user()));

            $this->closeModal();

            $this->notification()->success(
                "Opération réussite",
                "Votre envoi d'annulation a bien été envoyée."
            );
        } catch (\Exception $exception) {
            $this->handleError($exception);
        }
    }

    public function openAskCancelModal(Reservation $reservation)
    {
        $this->askCancelCard = true;
        $this->selectedReservation = $reservation;
    }

    public function closeModal()
    {
        $this->editAskCard = false;
        $this->askCancelCard = false;
        $this->selectedReservation = null;
        $this->message = null;

        $this->resetErrorBag();
    }

    private function handleError(\Exception $exception)
    {
        $this->closeModal();

        $this->notification()->error(
            $title = 'Erreur pendant le traitement',
            $description = 'Une erreur est survenue pendant votre demande, veuillez essayer ultérieurement'
        );

        if (\App::environment(['beta', 'prod'])) {
            Log::channel('sentry')->error("Une erreur s'est produite", [
                'exception' => $exception,
                'selectedReservation' => $this->selectedReservation
            ]);
        }

        if (\App::environment(['local'])) {
            ray($this->selectedReservation);
            ray()->exception($exception);
        }
    }
}
