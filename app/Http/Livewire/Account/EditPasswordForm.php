<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EditPasswordForm extends Component
{
    public User $user;
    public string $password = '';

    public function mount(User $account): void
    {
        $this->user = $account;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.account.edit-password-form')
            ->layout('components.layout');
    }

    protected function getRules(): array
    {
        return [
            'password' => 'required|current_password:web'
        ];
    }

    protected function getValidationAttributes(): array
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
