<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class EntrepriseForm extends Component
{
    use Actions;

    public Entreprise $entreprise;

    public function mount(Entreprise $entreprise): void
    {
        $this->entreprise = $entreprise;

        $this->entreprise->is_actif = $entreprise->is_actif;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return view('livewire.entreprise.entreprise-form')
            ->layout('components.layout');
    }

    /**
     * @return string[]
     */
    protected function getRules(): array
    {
        return [
            'entreprise.nom' => 'required',
            'entreprise.responsable_name' => 'nullable',
            'entreprise.is_actif' => 'boolean'
        ];
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        try {
            if ($this->entreprise->exists) {
                $this->entreprise->update();
                $this->notification([
                    'title' => 'Entreprise modifiée.',
                    'description' => "L'entreprise a bien été modifiée.",
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            } else {
                $this->entreprise->save();
                $this->notification([
                    'title' => 'Entreprise créée.',
                    'description' => "L'entreprise a bien été créée.",
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
                "Une erreur est survenue lors du traitement"
            );
            if (App::environment(['local'])) {
                ray([
                    'entreprise' => $this->entreprise
                ])->exception($exception);
            }
            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création entreprise", [
                    'exception' => $exception,
                    'entreprise' => $this->entreprise
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.entreprises.index'));
    }
}
