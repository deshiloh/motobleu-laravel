<?php

namespace App\Http\Livewire\CostCenter;

use App\Models\CostCenter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use WireUi\Traits\Actions;

class CostCenterForm extends Component
{
    use Actions;

    public CostCenter $costCenter;

    public function render()
    {
        return view('livewire.cost-center.cost-center-form')
            ->layout('components.layout');
    }

    public function mount(CostCenter $costCenter)
    {
        $this->costCenter = $costCenter;
        if (!$this->costCenter->exists) {
            $this->costCenter->is_actif = true;
        }
    }

    protected function getRules()
    {
        return [
            'costCenter.nom' => 'required',
            'costCenter.is_actif' => 'boolean'
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->costCenter->exists) {
                $this->costCenter->update();
                $this->notification()->success("Cost Center modifié.", "Le Cost Center a bien été modifié.");
            } else {
                $this->costCenter->save();
                $this->costCenter = new CostCenter();
                $this->notification()->success("Cost Center créé.", "Le Cost Center a bien été créé.");
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                "Erreur pendant le traitement",
                "Une erreur est survenue pendant le traitement.");

            if (App::environment(['local'])) {
                ray([
                    'cost_center' => $this->costCenter
                ])->exception($exception);
            }

            if (App::environment('prod')) {
                Log::channel("sentry")->error('Erreur pendant ajout cost center', [
                    'user_id' => \Auth::user()->id,
                    'email' => \Auth::user()->email,
                    'exception' => $exception,
                    'data' => $this->costCenter
                ]);
            }
        }
    }
}
