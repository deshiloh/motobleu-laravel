<?php

namespace App\Http\Livewire\Pilote;

use App\Models\Pilote;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class PiloteForm extends Component
{
    use Actions;

    public Pilote $pilote;

    public function mount(Pilote $pilote)
    {
        $this->pilote = $pilote;
    }

    public function render()
    {
        return view('livewire.pilote.pilote-form')
            ->layout('components.layout');
    }

    protected function getRules()
    {
        return [
            'pilote.nom' => 'required',
            'pilote.prenom' => 'required',
            'pilote.telephone' => 'required',
            'pilote.email' => 'required|email',
            'pilote.entreprise' => 'nullable',
            'pilote.adresse' => 'nullable',
            'pilote.adresse_complement' => 'nullable',
            'pilote.code_postal' => 'nullable',
            'pilote.ville' => 'nullable',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->pilote->exists) {
                $this->pilote->update();
                $this->notification()->success(
                    "Pilote modifé.",
                    "Le pilote a bien été modifié."
                );
            } else {
                $this->pilote->save();
                $this->notification()->success(
                    "Pilote créé.",
                    "Le pilote a bien été créé."
                );
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                "Erreur de traitement",
                "Une erreur est survenue pendant le traitement"
            );
            if (App::environment(['local'])) {
                ray([
                    'pilote' => $this->pilote
                ])->exception($exception);
            }
            // TODO Sentry in production
        }
    }
}
