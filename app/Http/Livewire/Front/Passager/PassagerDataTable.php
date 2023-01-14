<?php

namespace App\Http\Livewire\Front\Passager;

use App\Models\Passager;
use Livewire\Component;

class PassagerDataTable extends Component
{
    public int $perPage = 10;

    public function render()
    {
        return view('livewire.front.passager.passager-data-table', [
            'passagers' => Passager::where([
                'user_id' => \Auth::user()->id,
                'is_actif' => true
            ])
                ->orderBy('nom', 'asc')
                ->paginate($this->perPage)
        ])
            ->layout('components.front-layout');
    }
}
