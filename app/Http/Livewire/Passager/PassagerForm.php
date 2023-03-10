<?php

namespace App\Http\Livewire\Passager;

use App\Models\Passager;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class PassagerForm extends Component
{
    use Actions;

    public Passager $passager;

    public function mount(Passager $passager): void
    {
        $this->passager = $passager;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.passager.passager-form')
            ->layout('components.layout');
    }

    /**
     * @return string[]
     */
    protected function getRules(): array
    {
        $rules = [
            'passager.nom' => 'required',
            'passager.email' => 'required|email',
            'passager.telephone' => 'nullable',
            'passager.portable' => 'nullable',
            'passager.user_id' => 'required',
            'passager.cost_center_id' => 'nullable',
            'passager.type_facturation_id' => 'nullable',
        ];

        return $rules;
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->validate();
        try {
            if ($this->passager->exists) {
                $this->passager->update();
                $this->notification()->success(
                    $title = 'Passager modifié',
                    $description = 'Le passager a bien été modifié'
                );
            } else {
                $this->passager->save();
                $this->notification()->success(
                    $title = 'Passager créé',
                    $description = 'Le passager a bien été créé'
                );
                $this->passager = new Passager();
            }
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'passager' => $this->passager
                ])->exception($exception);
            }

            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'un passager", [
                    'exception' => $exception,
                    'passager' => $this->passager
                ]);
            }
        }
    }
}
