<?php

namespace App\Http\Livewire\Front\Account;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class AccountDataTable extends Component
{
    use WithPagination, Actions;

    public int $perPage = 20;

    public function render()
    {
        return view('livewire.front.account.account-data-table', [
            'users' => User::whereHas('entreprises', function (Builder $query) {
                $query->whereIn('id', \Auth::user()->entreprises()->pluck('id')->toArray());
            })->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }

    /**
     * Suppression d'un compte
     * @param int $idUser
     * @return void
     */
    public function deleteAccountAction(int $idUser): void
    {
        $user = User::findOrFail($idUser);

        try {
            foreach ($user->entreprises as $entreprise) {
                $entreprise->users()->detach($user);
            }

            $user->deleteQuietly();

            $this->notification()->success(
                title: "Opération réussite",
                description: "Utilisateur correctement supprimé"
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur est survenue",
                description: "Une erreur est survenue pendant la suppression de l'utilisateur"
            );

            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (\App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la suppresion de l'utilisateur", [
                    'exception' => $exception,
                    'user' => $user
                ]);
            }
        }
    }
}
