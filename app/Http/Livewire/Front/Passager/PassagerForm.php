<?php

namespace App\Http\Livewire\Front\Passager;

use App\Models\Passager;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class PassagerForm extends Component
{
    use Actions;

    public Passager $passager;

    public function mount(Passager $passager)
    {
        $this->passager = $passager;
    }

    public function render()
    {
        return view('livewire.front.passager.passager-form')
            ->layout('components.front-layout');
    }

    /**
     * @return string[]
     */
    protected function getRules(): array
    {
        $rules = [
            'passager.nom' => 'required',
            'passager.email' => 'required|email',
            'passager.telephone' => 'required',
            'passager.portable' => 'required',
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
                $this->passager->user()->associate(\Auth::user());
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
            // TODO Sentry en production
        }
    }
}
