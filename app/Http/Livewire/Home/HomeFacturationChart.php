<?php

namespace App\Http\Livewire\Home;

use Livewire\Component;

class HomeFacturationChart extends Component
{
    public array $dataset = [];

    public function render()
    {
        return view('livewire.home.home-facturation-chart');
    }
}
