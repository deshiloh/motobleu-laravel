<?php

namespace App\Http\Livewire\Front\Address;

use App\Models\AdresseReservation;
use Livewire\Component;
use WireUi\Traits\Actions;

class AddressForm extends Component
{
    use Actions;
    public AdresseReservation $adresseReservation;

    protected array $rules = [
        'adresseReservation.adresse' => 'required',
        'adresseReservation.adresse_complement' => 'nullable',
        'adresseReservation.code_postal' => 'required',
        'adresseReservation.ville' => 'required',
    ];

    public function mount(AdresseReservation $address)
    {
        $this->adresseReservation = $address;
    }

    public function render()
    {
        return view('livewire.front.address.address-form')
            ->layout('components.front-layout');
    }

    public function save()
    {
        $this->validate();

        try {
            $this->adresseReservation->user()->associate(\Auth::user());
            $this->adresseReservation->save();
            $this->notification()->success(
                title: 'Opération réussite',
                description: "L'adresse a bien été enregistrée."
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: 'Erreur',
                description: "Une erreur s'est produite pendant l'opération, veuillez réessayer ultérieurement."
            );
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
            if (\App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Une erreur s'est produite pendant la création d'une adresse de réservation", [
                    'exception' => $exception,
                    'address' => $this->adresseReservation
                ]);
            }
        }
    }
}
