<?php

namespace App\Http\Livewire\Localisation;

use App\Models\Localisation;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class LocalisationDataTable extends Component
{
    use WithPagination, WithSorting;

    public int $perPage = 20;
    public string $sortField = 'nom';
    public string $search = '';

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.localisation.localisation-data-table', [
            'localisations' => Localisation::search($this->search)->query(function ($q) {
                $q->orderBy($this->sortField, $this->sortDirection);
            })->paginate($this->perPage)
        ]);
    }
}
