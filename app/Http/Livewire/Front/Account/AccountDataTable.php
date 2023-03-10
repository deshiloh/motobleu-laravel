<?php

namespace App\Http\Livewire\Front\Account;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class AccountDataTable extends Component
{
    public int $perPage = 20;
    private array $entreprises;

    public function mount()
    {
        $this->entreprises = \Auth::user()->entreprises()->pluck('id')->toArray();
    }

    public function render()
    {
        return view('livewire.front.account.account-data-table', [
            'users' => User::whereHas('entreprises', function (Builder $query) {
                $query->whereIn('id', $this->entreprises);
            })->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }
}
