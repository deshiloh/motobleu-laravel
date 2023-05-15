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

    public function mount(User $account): void
    {
        $this->user = $account;

        if (!$this->user->exists) {
            $this->user->is_actif = true;
            $this->user->is_admin = true;
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
            'user.is_admin' => 'boolean',
            'user.is_actif' => 'boolean',
        ];

        if ($this->user->exists) {
            $rules['user.email'] = 'required|email';
        } else {
            $rules['user.email'] = 'required|email|unique:users,email';
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

    public function save()
    {
        $this->validate();

        try {
            if ($this->user->exists) {

                $this->user->update();

                $this->notification()->success(
                    $title = 'Compte modifié',
                    $description = 'Le compte a bien été modifié'
                );

            } else {
                $this->user->password = Hash::make('test');

                $this->user->save();

                $this->notification()->success(
                    $title = 'Compte créé',
                    $description = 'Le compte a bien été créé'
                );
            }

            $this->handlePermission();
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'user' => $this->user
                ])->exception($exception);
            }

            if (App::environment('prod')) {
                Log::channel("sentry")->error('Erreur formulaire utilisateur', [
                    'user_id' => \Auth::user()->id,
                    'email' => \Auth::user()->email,
                    'exception' => $exception,
                    'data' => $this->user
                ]);
            }
        }
    }

    private function handlePermission()
    {
        if ($this->user->is_admin) {
            $this->user->removeRole('user');
            $this->user->assignRole('admin');
        } else {
            $this->user->removeRole('admin');
            $this->user->assignRole('user');
        }
    }
}
