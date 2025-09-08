<?php

namespace App\Livewire\AdresseReservation;

use App\Models\AdresseReservation;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class AdresseReservationForm extends Component
{
    use WireUiActions;

    public AdresseReservation $adresseReservation;

    public function mount(AdresseReservation $adresseReservation)
    {
        $this->adresseReservation = $adresseReservation;
    }

    public function render()
    {
        return view('livewire.adresse-reservation.adresse-reservation-form')
            ->layout('components.layout');
    }

    public function getRules()
    {
        return [
            'adresseReservation.adresse' => 'required',
            'adresseReservation.adresse_complement' => 'nullable',
            'adresseReservation.code_postal' => 'required',
            'adresseReservation.ville' => 'required',
            'adresseReservation.user_id' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->adresseReservation->exists) {
                $this->adresseReservation->update();

                $this->notification()->send([
                    'title' => 'Adresse modifiée.',
                    'description' => "L'adresse a bien été modifiée.",
                    'icon' => 'success',
                    'timeout' => config('wireui.timeout'),
                    'onTimeout' => [
                        'method' => 'redirectToList'
                    ],
                    'onClose' => [
                        'method' => 'redirectToList'
                    ]
                ]);
            } else {
                $this->adresseReservation->save();

                $this->notification()->send([
                    'title' => 'Adresse créée.',
                    'description' => "L'adresse a bien été créée.",
                    'icon' => 'success',
                    'timeout' => config('wireui.timeout'),
                    'onTimeout' => [
                        'method' => 'redirectToList'
                    ],
                    'onClose' => [
                        'method' => 'redirectToList'
                    ]
                ]);

                $this->adresseReservation = new AdresseReservation();
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                "Erreur pendant le traitement",
                "Une erreur est survenue pendant le traitement"
            );
            if (App::environment(['local'])) {
                ray([
                    'adresse_reservation' => $this->adresseReservation
                ])->exception($exception);
            }
            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'une adresse réservation", [
                    'exception' => $exception,
                    'address' => $this->adresseReservation
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.adresse-reservation.index'));
    }
}
