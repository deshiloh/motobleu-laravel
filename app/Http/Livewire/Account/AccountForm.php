<?php

namespace App\Http\Livewire\Account;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use WireUi\Traits\Actions;

class AccountForm extends Component
{
    use Actions;

    public User $user;
    public bool $isAdmin = false;

    public function mount(User $account): void
    {
        $this->user = $account;

        if (!$this->user->exists) {
            $this->user->is_actif = true;
            $this->user->is_admin = true;
        } else {
            $this->isAdmin = $this->user->is_admin_role;
        }
    }

    protected function getRules(): array
    {
        $rules = [
            'user.nom' => 'required',
            'user.prenom' => 'required',
            'user.telephone' => 'nullable',
            'user.adresse' => 'nullable',
            'user.adresse_bis' => 'nullable',
            'user.code_postal' => 'nullable',
            'user.ville' => 'nullable',
            'user.is_actif' => 'boolean',
            'isAdmin' => 'boolean',
            'user.email' => 'required|email|unique:users,email'

        ];

        if ($this->user->exists) {
            $rules['user.email'] = 'required|email';
        }

        return $rules;
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
            if ($this->user->exists) {

                $this->user->update();

                $this->notification([
                    'title' => 'Compte modifié',
                    'description' => 'Le compte a bien été modifié',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);

            } else {
                $this->user->password = Hash::make(uniqid());

                $this->user->save();

                $this->notification([
                    'title' => 'Compte créé',
                    'description' => 'Le compte a bien été créé',
                    'icon' => 'success',
                    'onClose' => [
                        'method' => 'redirectToList'
                    ],
                    'timeout' => config('wireui.timeout')
                ]);
            }

            $this->handlePermission();
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'user' => $this->user
                ])->exception($exception);
            }

            if (App::environment('prod', 'beta')) {
                Log::channel("sentry")->error('Erreur formulaire utilisateur', [
                    'user_id' => \Auth::user()->id,
                    'email' => \Auth::user()->email,
                    'exception' => $exception,
                    'data' => $this->user
                ]);
            }
        }
    }

    public function redirectToList(): void
    {
        $this->redirect(route('admin.accounts.index'));
    }

    private function handlePermission(): void
    {
        $this->user->removeRole('user_ardian');
        $this->user->removeRole('admin');
        $this->user->removeRole('user');

        if ($this->isAdmin) {
            $this->user->assignRole('admin');
        } else {
            if ($this->user->is_ardian) {
                $this->user->assignRole('user_ardian');
                return;
            }

            $this->user->assignRole('user');
        }
    }
}
