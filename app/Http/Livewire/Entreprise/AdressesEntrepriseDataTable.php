<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class AdressesEntrepriseDataTable extends Component
{
    use Actions;

    public Entreprise $entreprise;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.entreprise.adresses-entreprise-data-table', [
            'adresses' => AdresseEntreprise::where('entreprise_id', $this->entreprise->id)->get()
        ]);
    }

    public function deleteAddress(AdresseEntreprise $adresseEntreprise): void
    {
        try {
            $adresseEntreprise->delete();

            $this->notification()->success(
                title: "Opération réussite !",
                description: "L'adresse a bien été supprimée."
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur est survenue",
                description: "Une erreur est survenue pendant la suppression de l'adresse"
            );

            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (\App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la suppression de l'adresse", [
                    'exception' => $exception,
                    'user' => $adresseEntreprise
                ]);
            }
        }
    }
}
