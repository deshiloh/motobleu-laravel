<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;

class SettingsForm extends Component
{
    public function render()
    {
        return view('livewire.settings.settings-form')->layout('components.layout');
    }
}
