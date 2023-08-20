<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\Actions;

class EditPasswordForm extends Component
{
    use Actions;

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

    public function editAction(): void
    {
        $this->validate();

        $this->user->update([
            'password' => Hash::make($this->password)
        ]);

        $this->notification([
            'title' => 'Mot de pass changé.',
            'description' => 'Le mot de passe a bien été changé.',
            'icon' => 'success',
            'onClose' => [
                'method' => 'redirectToList'
            ],
            'timeout' => config('wireui.timeout')
        ]);
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.accounts.index'));
    }
}
