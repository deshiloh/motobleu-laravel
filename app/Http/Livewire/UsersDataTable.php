<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class UsersDataTable extends Component
{
    use WithPagination, WithSorting;

    public int $perPage = 15;
    public string $search = '';
    public string $sortField = 'email';

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.users-data-table', [
            'users' => User::search('email', $this->search) // @phpstan-ignore-line
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
