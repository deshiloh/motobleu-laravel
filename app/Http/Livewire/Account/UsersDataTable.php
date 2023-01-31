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
        return view('livewire.account.users-data-table', [
            'users' => User::query()
                ->when($this->search != '', function (Builder $query, $search) {
                    return $query
                        ->where('nom', 'like', $search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
