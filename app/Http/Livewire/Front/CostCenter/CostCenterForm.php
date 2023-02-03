<?php

namespace App\Http\Livewire\Front\CostCenter;

use App\Models\CostCenter;
use Livewire\Component;
use WireUi\Traits\Actions;

class CostCenterForm extends Component
{
    use Actions;

    public CostCenter $costCenter;
    public bool $contextNewCostCenter;

    protected array $rules = [
        'costCenter.nom' => 'required',
        'costCenter.is_actif' => 'boolean',
    ];

    public function mount(CostCenter $center)
    {
        $this->costCenter = $center;
        $this->contextNewCostCenter = !$center->exists;

        if ($this->contextNewCostCenter) {
            $this->costCenter->is_actif = true;
        }
    }

    public function render()
    {
        return view('livewire.front.cost-center.cost-center-form')
            ->layout('components.front-layout');
    }

    public function save()
    {
        $this->validate();

        try {
            $this->costCenter->save();
            $this->notification()->success(
                title: "Opération réussite",
                description: $this->contextNewCostCenter ? "Cost Center créé." : "Cost Center modifé"
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Erreur",
                description: "Une erreur est survenue, veuillez essayer ultérieurement."
            );
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
            if (\App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'un Cost Center", [
                    'exception' => $exception,
                    'CostCenter' => $this->costCenter
                ]);
            }
        }
    }
}
