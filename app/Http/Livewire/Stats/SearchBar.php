<?php

namespace App\Http\Livewire\Stats;

use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Component;

class SearchBar extends Component
{
    public string $selectedYear = '';
    public ?string $selectedEntreprise = null;
    public array $years = [];

    public function mount(): void
    {
        if ($this->selectedYear === '') {
            $this->selectedYear = (string) Carbon::now()->year;
        }
    }

    public function render()
    {
        $this->years = $this->getAvailableYears();

        return view('livewire.stats.search-bar');
    }

    private function getAvailableYears(): array
    {
        return Reservation::distinct()
            ->orderBy('pickup_date', 'desc')
            ->pluck(\DB::raw('YEAR(pickup_date) as year'))
            ->unique()
            ->toArray();
    }

    public function updatedSelectedYear($value): void
    {
        $this->emit('onChangeSelectedYear', $value);
    }

    public function updatedSelectedEntreprise($value): void
    {
        $this->emit('onChangeSelectedEntreprise', $value);
    }
}
