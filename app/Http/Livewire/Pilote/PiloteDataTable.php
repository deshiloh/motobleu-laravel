<?php

namespace App\Http\Livewire\Pilote;

use App\Models\Pilote;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class PiloteDataTable extends Component
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
        return view('livewire.pilote.pilote-data-table', [
            'pilotes' => Pilote::query()
                ->when($this->search, function (Builder $builder, $search) {
                    $builder->where('nom', 'like', $search . '%')
                        ->orWhere('prenom', 'like', $search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
