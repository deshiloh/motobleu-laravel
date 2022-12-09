<?php

namespace App\Http\Livewire\Entreprise;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use WireUi\Traits\Actions;

class UsersEntrepriseDataTable extends Component
{
    use Actions;

    public Entreprise $entreprise;
    public ?string $userId = '';
    public Collection $users;

    public function mount(): void
    {
        $this->users = new Collection();

        $this->users = $this->entreprise->users()->get();
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.entreprise.users-entreprise-data-table');
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
                $this->users = $this->entreprise->users()->get();
                $this->userId = '';
            }

        } else {
            $this->notification()->error(
                $title = 'Action impossible',
                $description = 'Vous devez sélectionner un compte.'
            );
        }
    }
}
