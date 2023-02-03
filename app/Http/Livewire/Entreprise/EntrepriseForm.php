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
            'entreprise.responsable_name' => 'required',
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
                $this->notification()->success(
                    "Entreprise modifiée.",
                    "L'entreprise a bien été modifiée."
                );
            } else {
                $this->entreprise->save();
                $this->notification()->success(
                    "Entreprise créée.",
                    "L'entreprise a bien été créée."
                );
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
}
