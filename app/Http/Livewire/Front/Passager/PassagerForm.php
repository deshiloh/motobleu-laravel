<?php

namespace App\Http\Livewire\Front\Passager;

use Livewire\Component;

class PassagerForm extends Component
{
    public function render()
    {
        return view('livewire.front.passager.passager-form')
            ->layout('components.front-layout');
    }
}
