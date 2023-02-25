<?php

namespace App\Http\Livewire\Entreprise;

use App;
use App\Models\Entreprise;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Log;
use WireUi\Traits\Actions;

class UsersEntrepriseDataTable extends Component
{
    use Actions, WithPagination;

    public Entreprise $entreprise;
    public ?string $userId = '';
    public array $exclude = [];

    public function mount()
    {
        $this->exclude = $this->getExclude();
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.entreprise.users-entreprise-data-table', [
            'users' => $this->entreprise
                ->users()
                ->orderBy('nom')
                ->paginate(10, ['*'], 'usersPage')
        ]);
    }

    private function getExclude(): array
    {
        return $this->entreprise->users()->pluck('id')->toArray();
    }


    /**
     * @return string[]
     */
    protected function getRules(): array
    {
        return [
            'user' => 'nullable'
        ];
    }

    /**
     * @return void
     */
    public function attach(): void
    {
        if ($user = User::find($this->userId)) {
            if ($this->entreprise->users()->where('id', $user->id)->exists()) {
                $this->notification()->error(
                    $title = 'Action impossible',
                    $description = 'Le compte est déjà rattaché à l\'entreprise.'
                );
            } else {
                $this->entreprise->users()->attach($user);
                $this->exclude = $this->getExclude();

                $this->userId = '';
                $this->notification()->success(
                    $title = 'Opération réussite',
                    $description = 'Le compte a bien été ajouté a l\'entreprise.'
                );
            }

        } else {
            $this->notification()->error(
                $title = 'Action impossible',
                $description = 'Vous devez sélectionner un compte.'
            );
        }
    }

    public function detach(User $user)
    {
        try {
            $this->entreprise->users()->detach($user);

            $this->notification()->success(
                $title = 'Opération réussite',
                $description = 'Le compte a bien été détaché.'
            );
        } catch (Exception $exception) {
            $this->notification()->error(
                $title = 'Erreur',
                $description = 'Une erreur est survenue.'
            );
            if (App::environment(['prod', 'beta'])) {
                Log::channel("sentry")->error("Erreur pendant le détachement d'un user à une entreprise", [
                    'exception' => $exception,
                    'entreprise' => $this->entreprise,
                    'user' => $user
                ]);
            }
        }
    }
}
