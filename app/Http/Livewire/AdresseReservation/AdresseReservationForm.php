<?php

namespace App\Http\Livewire\AdresseReservation;

use App\Models\AdresseReservation;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class AdresseReservationForm extends Component
{
    use Actions;

    public AdresseReservation $adresseReservation;

    public function mount(AdresseReservation $adresseReservation)
    {
        $this->adresseReservation = $adresseReservation;
    }

    public function render()
    {
        return view('livewire.adresse-reservation.adresse-reservation-form')
            ->layout('components.admin-layout');
    }

    protected function getRules()
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
                $this->notification()->success("Adresse modifiée", "L'adresse a bien été modifée.");
            } else {
                $this->adresseReservation->save();
                $this->notification()->success("Adresse créée", "L'adresse a bien été créée.");
                $this->adresseReservation = new AdresseReservation();
            }
        }catch (\Exception $exception) {
            $this->notification()->error(
                "Erreur pendant le traitement",
                "Une erreur est survenue pendant le traitement"
            );
            if (App::environment(['local'])) {
                ray([
                    'adresse_reservation' => $this->adresseReservation
                ])->exception($exception);
            }
            // TODO Sentry en production
        }
    }
}
