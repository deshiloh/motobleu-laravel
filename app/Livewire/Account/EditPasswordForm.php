<?php

namespace App\Livewire\Account;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class EditPasswordForm extends Component
{
    use WireUiActions;

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

    public function getRules(): array
    {
        return [
            'password' => 'required'
        ];
    }

    public function getValidationAttributes(): array
    {
        return [
            'password' => 'mot de passe'
        ];
    }

    public function editAction(): void
    {
        $this->validate();

        $this->user->update([
            'password' => Hash::make($this->password)
        ]);

        $this->notification()->send([
            'title' => 'Mot de passe changé.',
            'description' => 'Le mot de passe a bien été changé.',
            'icon' => 'success',
            'timeout' => config('wireui.timeout'),
            'onTimeout' => [
                'method' => 'redirectToList'
            ],
            'onClose' => [
                'method' => 'redirectToList'
            ]
        ]);
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.accounts.index'));
    }
}
