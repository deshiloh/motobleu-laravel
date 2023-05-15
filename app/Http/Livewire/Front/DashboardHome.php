<?php

namespace App\Http\Livewire\Front;

use Livewire\Component;

class DashboardHome extends Component
{
    public function render()
    {
        ray(\Auth::user()->getAllPermissions()->toArray());

        return view('livewire.front.dashboard-home')
            ->layout('components.front-layout');
    }
}
