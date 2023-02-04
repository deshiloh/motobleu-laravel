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

class UsersDataTable extends Component
{
    use WithPagination, WithSorting;

    public int $perPage = 6;
    public string $search = '';
    public string $sortField = 'nom';

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        ray()->showQueries();
        return view('livewire.account.users-data-table', [
            'users' => User::query()
                ->where('nom', 'like', '%'. $this->search . '%')
                ->orWhere('prenom', 'like', '%'.$this->search.'%')
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
