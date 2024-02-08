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
    public $commission = 15;

    public function mount(Pilote $pilote)
    {
        $this->pilote = $pilote;
        $this->commission = $this->pilote->commission;
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
            'pilote.telephone' => 'nullable',
            'pilote.email' => 'required|email',
            'pilote.entreprise' => 'nullable',
            'pilote.adresse' => 'nullable',
            'pilote.adresse_complement' => 'nullable',
            'pilote.code_postal' => 'nullable',
            'pilote.ville' => 'nullable',
            'commission' => 'required'
        ];
    }

    public function save()
    {
        $this->validate();

        $this->pilote->commission = $this->commission;

        try {
            if ($this->pilote->exists) {
                $this->pilote->update();
                $this->notification([
                    'title' => 'Pilote modifié.',
                    'description' => 'Le pilote a bien été modifié',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            } else {
                $this->pilote->save();
                $this->notification([
                    'title' => 'Pilote créé.',
                    'description' => 'Le pilote a bien été créé',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
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

            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'un pilote", [
                    'exception' => $exception,
                    'pilote' => $this->pilote
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.pilotes.index'));
    }
}
