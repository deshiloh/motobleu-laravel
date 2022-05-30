<?php

namespace App\Http\Livewire;

use App\Models\Entreprise;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EntreprisesDataTable extends Component
{
    use WithPagination, WithSorting;

    public string $search = '';
    public string $sortField = 'nom';
    public int $perPage = 8;

    /**
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('livewire.entreprises-data-table', [
            'entreprises' => Entreprise::search('nom', $this->search) // @phpstan-ignore-line
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
