<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class UsersDataTable extends Component
{
    use WithPagination, WithSorting, Actions;

    public int $perPage = 6;
    public string $search = '';
    public string $sortField = 'nom';
    public ?int $selectedEntreprise = null;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.account.users-data-table', [
            'users' => User::query()
                ->when($this->search, function (Builder $query) {
                    $query->where('nom', 'like', '%'. $this->search . '%');
                    $query->orWhere('prenom', 'like', '%'.$this->search.'%');
                })
                ->when($this->selectedEntreprise, function (Builder $query) {
                    $query->whereHas('entreprises', function (Builder $query) {
                        $query->where('id', $this->selectedEntreprise);
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    /**
     * Permet de désactiver le compte sélectionné
     * @param User $user
     * @return void
     */
    public function disableAccount(User $user): void
    {
        $user->is_actif = false;
        $user->update();

        $this->notification()->success(
            'Opération réussite',
            "Le compte " . $user->full_name . ' a bien été désactivé.'
        );
    }

    /**
     * Permet d'activer le compte sélectionné
     * @param User $user
     * @return void
     */
    public function enableAccount(User $user): void
    {
        $user->is_actif = true;
        $user->update();

        $this->notification()->success(
            'Opération réussite',
            "Le compte " . $user->full_name . ' a bien été activé.'
        );
    }
}
