<?php

namespace App\Http\Livewire\TypeFacturation;

use App\Models\TypeFacturation;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\Actions;

class TypeFacturationForm extends Component
{
    use Actions;

    public TypeFacturation $typeFacturation;

    public function mount(TypeFacturation $typefacturation)
    {
        $this->typeFacturation = $typefacturation;
    }

    public function render()
    {
        return view('livewire.type-facturation.type-facturation-form')
            ->layout('components.admin-layout');
    }

    protected function getRules()
    {
        return [
            'typeFacturation.nom' => 'required'
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->typeFacturation->exists) {
                $this->typeFacturation->update();
                $this->notification()->success(
                    "Type de facturation modifié.",
                    "Type de facturation correctement modifié."
                );
            } else {
                $this->typeFacturation->save();
                $this->notification()->success(
                    "Type de facturation créé.",
                    "Type de facturation correctement créé."
                );
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                "Erreur pendant le traitement",
                "Une erreur est survenue pendant le traitement"
            );
            if (App::environment(['local'])) {
                ray([
                    'typeFacturation' => $this->typeFacturation
                ])->exception($exception);
            }
            // TODO Sentry en production
        }
    }
}
