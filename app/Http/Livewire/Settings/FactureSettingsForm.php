<?php

namespace App\Http\Livewire\Settings;

use app\Settings\BillSettings;
use Livewire\Component;
use WireUi\Traits\Actions;

class FactureSettingsForm extends Component
{
    use Actions;

    public array $entreprisesXls;
    public array $entreprisesCode;
    public array $entreprisesCostCenterFacturation;

    protected array $rules = [
        'entreprisesXls' => 'nullable',
        'entreprisesCode' => 'nullable',
        'entreprisesCostCenterFacturation' => 'nullable',
    ];

    public function mount(BillSettings $billSettings)
    {
        $this->entreprisesXls = $billSettings->entreprises_xls_file ?? [];
        $this->entreprisesCostCenterFacturation = $billSettings->entreprises_cost_center_facturation ?? [];
        $this->entreprisesCode = $billSettings->entreprises_command_field ?? [];
    }

    public function render()
    {
        return view('livewire.settings.facture-settings-form');
    }

    public function save(BillSettings $billSettings)
    {
        $this->validate();

        try {
            $billSettings->entreprises_xls_file = $this->entreprisesXls;
            $billSettings->entreprises_cost_center_facturation = $this->entreprisesCostCenterFacturation;
            $billSettings->entreprises_command_field = $this->entreprisesCode;
            $billSettings->save();

            $this->notification()->success("Opération réussite", "Les paramètres de facturations ont bien été modifiés.");
        } catch (\Exception $exception) {
            $this->notification()->error("Erreur", "Une erreur est survenue");
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
        }
    }
}
