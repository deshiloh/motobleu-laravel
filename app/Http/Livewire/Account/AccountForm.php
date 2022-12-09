<?php

namespace App\Http\Livewire\Account;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
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
            $this->user->is_actif = false;
            $this->user->is_admin_ardian = false;
        }
    }

    protected function getRules(): array
    {
        $rules = [
            'user.nom' => 'required',
            'user.prenom' => 'required',
            'user.telephone' => 'required',
            'user.adresse' => 'required',
            'user.adresse_bis' => 'nullable',
            'user.code_postal' => 'required',
            'user.ville' => 'required',
            'user.is_admin_ardian' => 'boolean',
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
                $this->reset(['user.nom']);
            } else {
                $this->user->password = Hash::make('test');
                $this->user->save();
                $this->notification()->success(
                    $title = 'Compte créé',
                    $description = 'Le compte a bien été créé'
                );
            }
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'user' => $this->user
                ])->exception($exception);
            }
            // TODO Sentry en production
        }
    }
}
