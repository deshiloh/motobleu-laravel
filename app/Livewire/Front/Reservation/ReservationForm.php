<?php

namespace App\Livewire\Front\Reservation;

use App\Livewire\Forms\FrontReservationForm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;
use WireUi\Traits\WireUiActions;

class ReservationForm extends Component
{
    use WireUiActions;
    public FrontReservationForm $form;

    public function mount(): void
    {
        $this->form->userId = auth()->id();
    }

    #[Layout('components.front-layout')]
    public function render(): View
    {
        return view('livewire.front.reservation.reservation-form');
    }

    public function redirectToList(): void
    {
        $this->redirectRoute('front.reservation.list', navigate: true);
    }

    public function createReservation(): void
    {
        try {
            $this->form->save();

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
            throw $e;
        } catch (Throwable $e) {
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
}
