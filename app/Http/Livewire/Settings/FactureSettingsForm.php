<?php

namespace App\Http\Livewire\Settings;

use app\Settings\BillSettings;
use Livewire\Component;
use WireUi\Traits\Actions;

class FactureSettingsForm extends Component
{
    use Actions;

    public array $entreprisesXls;
    public array $entreprisesCostCenterFacturation;
    public array $entreprisesWithoutCommandField;
    public string $rib;

    protected array $rules = [
        'entreprisesXls' => 'nullable',
        'entreprisesCostCenterFacturation' => 'nullable',
        'entreprisesWithoutCommandField' => 'nullable',
    ];

    public function mount(BillSettings $billSettings)
    {
        $this->entreprisesXls = $billSettings->entreprises_xls_file ?? [];
        $this->entreprisesCostCenterFacturation = $billSettings->entreprises_cost_center_facturation ?? [];
        $this->entreprisesWithoutCommandField = $billSettings->entreprise_without_command_field ?? [];
        $this->rib = $billSettings->rib ?? '';
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
            $billSettings->entreprise_without_command_field = $this->entreprisesWithoutCommandField;
            $billSettings->rib = $this->rib;
            $billSettings->save();

            $this->notification()->success("Opération réussite", "Les paramètres de facturations ont bien été modifiés.");
        } catch (\Exception $exception) {
            $this->notification()->error("Erreur", "Une erreur est survenue");
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
            if (\App::environment(['prod', 'local'])) {
                \Log::channel("sentry")->error("Erreur pendant la modification des paramètres BillSettings", [
                    'exception' => $exception,
                    'settings' => $billSettings
                ]);
            }
        }
    }
}
