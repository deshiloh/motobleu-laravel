<?php

namespace App\Livewire\Reservation;

use App;
use App\Livewire\Forms\AdminReservationForm;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;
use WireUi\Traits\WireUiActions;

class ReservationForm extends Component
{
    use WireUiActions;

    public AdminReservationForm $form;
    public Reservation $reservation;
    public bool $isSubmitting = false;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation;
    }

    #[Layout("components.layout")]
    public function render(): View
    {
        return view('livewire.reservation.reservation-form');
    }

    public function updated($property, $value): void
    {
        switch ($property) {
            case 'form.userId':
                if (is_null($value)) {
                    $this->form->passengerId = null;
                }
                break;
            default:
                break;
        }
    }

    /**
     * @throws Throwable
     */
    public function saveReservation(): void
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->form->validate();

            $this->form->createReservationWithoutValidation();

            // Reset du formulaire après création réussie
            $this->form->reset();

            $this->notification()->send([
                'icon' => 'success',
                'title' => 'Réservation créée avec succès!',
                'description' => 'Votre réservation a été enregistrée et sera traitée dans les plus brefs délais.',
                'onClose' => [
                    'method' => 'redirectToList',
                ],
            ]);
        } catch (ValidationException $e) {
            $this->isSubmitting = false;
            throw $e;
        } catch (Throwable $e) {
            $this->isSubmitting = false;

            $this->notification()->error(
                title: 'Erreur',
                description: 'Une erreur est survenue pendant la création de la réservation.'
            );

            if (App::environment(['local'])) {
                ray([
                    'form' => $this->form->all()
                ])->exception($e);
            }

            if (App::environment(['prod', 'beta'])) {
                Log::channel('sentry')->critical('Erreur pendant la création de réservation', [
                    'exception' => $e,
                    'form' => $this->form->all()
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirectRoute('admin.reservations.index', navigate: true);
    }
}
