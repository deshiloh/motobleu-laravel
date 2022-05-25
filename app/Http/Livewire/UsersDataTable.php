<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class UsersDataTable extends Component
{
    use WithPagination, WithSorting;

    public $perPage = 15;
    public $search = '';
    public $sortField = 'email';

    public function render()
    {
        return view('livewire.users-data-table', [
            'users' => User::search('email', $this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
