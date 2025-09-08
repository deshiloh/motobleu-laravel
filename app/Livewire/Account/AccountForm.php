<?php

namespace App\Livewire\Account;

use App\Livewire\Forms\UserForm;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class AccountForm extends Component
{
    use WireUiActions;

    public UserForm $form;
    public bool $isAdmin = false;

    public function mount($account = null): void
    {
        if ($account) {
            // If $account is already a User model, use it directly
            // Otherwise, treat it as an ID and find the user
            if ($account instanceof User) {
                $account = $account;
            } else {
                $account = User::findOrFail($account);
            }
        } else {
            $account = new User();
        }

        $this->form->setUser($account);

        if (!$account->exists) {
            $this->form->is_actif = true;
            $this->isAdmin = true;
        } else {
            // Only check for 'admin' role, not 'super admin' which can't be changed via toggle
            $this->isAdmin = $account->hasRole('admin');
        }
    }

    protected function rules(): array
    {
        return [
            'isAdmin' => 'boolean'
        ];
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.account.account-form')
            ->layout('components.layout')
        ;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $isNewUser = !$this->form->user || !$this->form->user->id;

            // Set password for new users before saving
            if ($isNewUser) {
                $this->form->user->password = Hash::make(uniqid());
            }

            // Use the UserForm save method
            $user = $this->form->save();

            if ($isNewUser) {
                $this->notification()->send([
                    'title' => 'Compte créé',
                    'description' => 'Le compte a bien été créé',
                    'icon' => 'success',
                    'timeout' => config('wireui.timeout'),
                    'onTimeout' => [
                        'method' => 'redirectToList'
                    ],
                    'onClose' => [
                        'method' => 'redirectToList'
                    ]
                ]);
            } else {
                $this->notification()->send([
                    'title' => 'Compte modifié',
                    'description' => 'Le compte a bien été modifié',
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

            $this->handlePermission($user);
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: 'Une erreur est survenue',
                description: 'Erreur pendant le traitement'
            );
            if (App::environment(['local'])) {
                ray([
                    'form' => $this->form->all()
                ])->exception($exception);
            }

            if (App::environment('prod', 'beta')) {
                Log::channel("sentry")->error('Erreur formulaire utilisateur', [
                    'user_id' => \Auth::user()->id,
                    'email' => \Auth::user()->email,
                    'exception' => $exception,
                    'data' => $this->form->all()
                ]);
            }

            // Re-throw in testing environment to help with debugging
            if (App::environment('testing')) {
                throw $exception;
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.accounts.index'));
    }

    private function handlePermission(User $user): void
    {
        // Don't modify super admin role - it's permanent
        if ($user->hasRole('super admin')) {
            // For super admin users, only manage the additional admin role
            if ($this->isAdmin && !$user->hasRole('admin')) {
                $user->assignRole('admin');
            } elseif (!$this->isAdmin && $user->hasRole('admin')) {
                $user->removeRole('admin');
            }
            return;
        }

        // For non-super admin users, manage roles normally
        // Remove roles safely (only if they exist)
        if ($user->hasRole('user_ardian')) {
            $user->removeRole('user_ardian');
        }
        if ($user->hasRole('admin')) {
            $user->removeRole('admin');
        }
        if ($user->hasRole('user')) {
            $user->removeRole('user');
        }

        if ($this->isAdmin) {
            $user->assignRole('admin');
        } else {
            if ($user->is_ardian) {
                $user->assignRole('user_ardian');
                return;
            }

            $user->assignRole('user');
        }
    }
}
