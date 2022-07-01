<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EditPasswordForm extends Component
{
    public User $user;
    public string $password = '';

    public function mount(User $account)
    {
        $this->user = $account;
    }

    public function render()
    {
        return view('livewire.account.edit-password-form')
            ->layout('components.admin-layout');
    }

    protected function getRules()
    {
        return [
            'password' => 'required|current_password:web'
        ];
    }

    protected function getValidationAttributes()
    {
        return [
            'password' => 'mot de passe'
        ];
    }

    public function editAction()
    {
        $this->validate();

        $this->user->update([
            'password' => Hash::make($this->password)
        ]);
    }
}
