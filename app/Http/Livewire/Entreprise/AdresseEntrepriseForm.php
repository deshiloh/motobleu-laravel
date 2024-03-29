<?php

namespace App\Http\Livewire\Entreprise;

use App\Enum\AdresseEntrepriseTypeEnum;
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

        if (!$this->adresseEntreprise->exists) {
            $this->adresseEntreprise->type = AdresseEntrepriseTypeEnum::FACTURATION->value;
        }
    }

    public function render()
    {
        return view('livewire.entreprise.adresse-entreprise-form')
            ->layout('components.layout');
    }

    protected function getRules(): array
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

                $this->notification([
                    'title' => 'Adresse modifiée.',
                    'description' => "L'adresse a bien été modifiée..",
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            } else {
                $this->entreprise->adresseEntreprises()->save($this->adresseEntreprise);
                $this->notification([
                    'title' => 'Adresse crééé.',
                    'description' => "L'adresse a bien été créée.",
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
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
            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'une adresse entreprise", [
                    'exception' => $exception,
                    'address' => $this->adresseEntreprise,
                    'entreprise' => $this->entreprise
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.entreprises.show', ['entreprise' => $this->entreprise]));
    }
}
