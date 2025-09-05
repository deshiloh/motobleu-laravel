<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class PermissionForm extends Component
{
    public function render()
    {
        return view('livewire.admin.permission-form')->layout('components.layout');
    }
}
