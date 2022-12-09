<?php

namespace App\Http\Livewire\TypeFacturation;

use App\Models\TypeFacturation;
use App\Traits\WithSorting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TypeFacturationDataTable extends Component
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
        return view('livewire.type-facturation.type-facturation-data-table', [
            'typefacturations' => TypeFacturation::search($this->search)->query(function ($q) {
                $q->orderBy($this->sortField, $this->sortDirection);
            })->paginate($this->perPage)
        ]);
    }
}
