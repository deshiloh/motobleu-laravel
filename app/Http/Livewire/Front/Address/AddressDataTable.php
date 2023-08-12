<?php

namespace App\Http\Livewire\Front\Address;

use App;
use App\Models\AdresseReservation;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class AddressDataTable extends Component
{
    use WithPagination, Actions;

    public string $search = "";
    public int $perPage = 20;
    public function render()
    {
        return view('livewire.front.address.address-data-table', [
            'addresses' => AdresseReservation::where('is_deleted', false)
                ->when(Auth::user()->is_admin_ardian, function (Builder $query) {
                    $query->whereHas('user.entreprises', function(Builder $query) {
                        $query->whereIn('id', Auth::user()->entreprises()->pluck('id')->toArray());
                    });
                }, function(Builder $query) {
                    $query->where('user_id', Auth::user()->id);
                })
                ->when($this->search, function (Builder $query, $search) {
                    $query->where('adresse', 'like', '%' . $search . '%');
                })
                ->orderBy('adresse')
                ->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }

    /**
     * Permet de désactiver une adresse de réservations
     * @param AdresseReservation $adresseReservation
     * @return void
     */
    public function toggleAddress(AdresseReservation $adresseReservation): void
    {
        try {
            $adresseReservation->update([
                'is_actif' => !$adresseReservation->is_actif
            ]);

            $this->notification()->success(
                title: "Opération réussite",
                description: "L'opération s'est bien déroulé"
            );
        } catch (Exception $exception) {
            $this->notification()->error(
                title: "Erreur",
                description: "Une erreur est survenue pendant l'opération"
            );

            if (App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                \Log::channel('sentry')->error("Erreur pendant l'activation ou désactivation d'une adresse", [
                    'exception' => $exception,
                    'adresse' => $adresseReservation
                ]);
            }
        }
    }

    public function deleteAddress(AdresseReservation $adresseReservation)
    {
        try {
            $adresseReservation->update([
                'is_deleted' => true
            ]);

            $this->notification()->success(
                title: "Opération réussite",
                description: "L'adresse a bien été supprimée."
            );
        } catch (Exception $exception) {
            $this->notification()->error(
                title: "Erreur",
                description: "Une erreur est survenue pendant l'opération, veuillez essayer ultérieurement."
            );

            if (App::environment(['local'])) {
                ray()->exception($exception);
            }
            // TODO Sentry
        }
    }
}
