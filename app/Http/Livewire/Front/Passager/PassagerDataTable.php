<?php

namespace App\Http\Livewire\Front\Passager;

use App\Models\Passager;
use Livewire\Component;
use WireUi\Traits\Actions;

class PassagerDataTable extends Component
{
    use Actions;

    public int $perPage = 10;

    public function render()
    {
        return view('livewire.front.passager.passager-data-table', [
            'passagers' => Passager::where([
                'user_id' => \Auth::user()->id,
                'is_actif' => true
            ])
                ->orderBy('nom', 'asc')
                ->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }

    /**
     * Demande confirmation de supprimer le passager
     * @param Passager $passager
     * @return void
     */
    public function deletePassenger(Passager $passager): void
    {
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => 'Save the information?',
            'acceptLabel' => 'Yes, save it',
            'method'      => 'confirmDeletePassenger',
            'params'      => $passager,
        ]);
    }

    /**
     * Suppression du passager après confirmation
     * @param Passager $passager
     * @return void
     */
    public function confirmDeletePassenger(Passager $passager): void
    {
        try {
            $passager->update([
                'is_actif' => false
            ]);

            $this->notification()->success(
                title: "Opération réussite",
                description: "Le passager a bien été supprimé."
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur vient de se produire",
                description: "Une erreur est survenue pendant la suppression du passager."
            );

            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            // TODO Sentry
        }

    }
}
