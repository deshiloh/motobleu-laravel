<?php

namespace App\Http\Livewire\Passager;

use App\Models\Passager;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PassagersDataTable extends Component
{
    use WithPagination, WithSorting;

    public string $search = '';
    public string $sortField = 'nom';
    public int $perPage = 10;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.passager.passagers-data-table', [
            'passagers' => Passager::search($this->search)->query(function ($q) {
                $q->orderBy($this->sortField, $this->sortDirection);
            })->paginate($this->perPage)
        ]);
    }
}
