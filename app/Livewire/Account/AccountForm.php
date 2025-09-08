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

    public function mount(User $account = null): void
    {
        if (!$account) {
            $account = new User();
        }
        
        $this->form->setUser($account);

        if (!$account->exists) {
            $this->form->is_actif = true;
            $this->isAdmin = true;
        } else {
            $this->isAdmin = $account->is_admin_role;
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
            $isNewUser = !$this->form->user || !$this->form->user->exists;
            $user = $this->form->save();

            if ($isNewUser) {
                $user->password = Hash::make(uniqid());
                $user->save();
            }

            if ($isNewUser) {
                $this->notification([
                    'title' => 'Compte créé',
                    'description' => 'Le compte a bien été créé',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            } else {
                $this->notification([
                    'title' => 'Compte modifié',
                    'description' => 'Le compte a bien été modifié',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            }

            $this->handlePermission($user);
        } catch (\Exception $exception) {
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
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.accounts.index'));
    }

    private function handlePermission(User $user): void
    {
        $user->removeRole('user_ardian');
        $user->removeRole('admin');
        $user->removeRole('user');

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
