<?php

namespace App\Http\Livewire\Localisation;

use App\Models\Localisation;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class LocalisationForm extends Component
{
    use Actions;

    public Localisation $localisation;

    public function mount(Localisation $localisation)
    {
        $this->localisation = $localisation;
    }

    public function render()
    {
        return view('livewire.localisation.localisation-form')
            ->layout('components.layout');
    }

    protected function getRules()
    {
        return [
            'localisation.nom' => 'required',
            'localisation.telephone' => 'nullable',
            'localisation.adresse' => 'nullable',
            'localisation.adresse_complement' => 'nullable',
            'localisation.code_postal' => 'nullable',
            'localisation.ville' => 'nullable',
            'localisation.is_actif' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->localisation->exists) {
                $this->localisation->update();

                $this->notification([
                    'title' => 'Localisation modifée.',
                    'description' => 'La localisation a bien été modifiée',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            } else {
                $this->localisation->save();

                $this->notification([
                    'title' => 'Localisation créée.',
                    'description' => 'La localisation a bien été créée',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);

                $this->localisation = new Localisation();
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                $title = "Erreur pendant le traitement.",
                $description = "Une erreur est survenue pendant le traitement."
            );
            if (App::environment(['local'])) {
                ray([
                    'localisation' => $this->localisation
                ])->exception($exception);
            }
            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'une localisation", [
                    'exception' => $exception,
                    'localisation' => $this->localisation
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.localisations.index'));
    }
}
