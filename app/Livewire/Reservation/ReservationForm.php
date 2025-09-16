<?php

namespace App\Livewire\Reservation;

use App\Livewire\Forms\AdminReservationForm;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class ReservationForm extends Component
{
    use WireUiActions;

    public AdminReservationForm $form;
    public Reservation $reservation;

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

    public function saveReservation(): void
    {
        $this->form->createReservation();
    }
}
