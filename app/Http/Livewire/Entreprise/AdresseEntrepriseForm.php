<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class AdresseEntrepriseForm extends Component
{
    use Actions;

    public AdresseEntreprise $adresseEntreprise;
    public Entreprise $entreprise;

    public function mount(Entreprise $entreprise, AdresseEntreprise $adress)
    {
        $this->entreprise = $entreprise;
        $this->adresseEntreprise = $adress;
    }

    public function render()
    {
        return view('livewire.entreprise.adresse-entreprise-form')
            ->layout('components.layout');
    }

    protected function getRules()
    {
        return [
            'adresseEntreprise.nom' => 'required',
            'adresseEntreprise.email' => 'required',
            'adresseEntreprise.adresse' => 'required',
            'adresseEntreprise.adresse_complement' => 'nullable',
            'adresseEntreprise.code_postal' => 'required',
            'adresseEntreprise.ville' => 'required',
            'adresseEntreprise.tva' => 'nullable',
            'adresseEntreprise.type' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->adresseEntreprise->exists) {
                $this->adresseEntreprise->update();
                $this->notification()->success(
                    "Adresse modifiée",
                    "L'adresse a bien été modifiée."
                );
            } else {
                $this->entreprise->adresseEntreprises()->save($this->adresseEntreprise);
                $this->notification()->success(
                    "Adresse crééé.",
                    "L'adresse a bien été créée."
                );
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                "Erreur pendant le traitement",
                "Une erreur est survenue pendant le traitement"
            );
            if (App::environment(['local'])) {
                ray([
                    'entreprise' => $this->entreprise,
                    'adresse_entreprise' => $this->adresseEntreprise
                ])->exception($exception);
            }
            // TODO Sentry en production
        }
    }
}
