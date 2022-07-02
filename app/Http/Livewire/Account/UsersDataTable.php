<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use MeiliSearch\Endpoints\Indexes;

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
            'users' => User::search($this->search, function (Indexes $melisearch, string $query, array $options) {
                $sort = [$this->sortField.':'.$this->sortDirection];
                $options['sort'] = $sort;
                return $melisearch->search($query, $options);
            }) // @phpstan-ignore-line
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
