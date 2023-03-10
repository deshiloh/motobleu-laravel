<?php

namespace App\Http\Livewire\Front\Account;

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
    public int $userId;
    public bool $contextNewUser;

    public function mount(User $account)
    {
        $this->user = $account;
        $this->userId = \Auth::user()->id;

        $this->contextNewUser = !$account->exists;

        if ($this->contextNewUser) {
            $this->user->is_actif = false;
            $this->user->is_admin_ardian = false;
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

    public function render()
    {
        return view('livewire.front.account.account-form')
            ->layout('components.front-layout');
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->contextNewUser) {
                $this->user->password = Hash::make('test');
                $this->user->save();
            }

            $this->user->entreprises()->attach(Entreprise::find(\Auth::user()->entreprises()->first()->id));

            $this->user->update();

            $this->notification()->success(
                title : $this->user->exists ? 'Compte modifié' : 'Compte créé',
                description : $this->user->exists ? 'Le compte a bien été modifié' : 'Le compte a bien été créé'
            );

            if ($this->contextNewUser) $this->resetFields();

        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'user' => $this->user
                ])->exception($exception);
            }
            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la création / édition d'un utilisateur", [
                    'exception' => $exception,
                    'user' => \Auth::user(),
                    'data_user' => $this->user
                ]);
            }
        }
    }

    private function resetFields()
    {
        $this->user->nom = null;
        $this->user->prenom = null;
        $this->user->email = null;
        $this->user->adresse = null;
        $this->user->adresse_bis = null;
        $this->user->code_postal = null;
        $this->user->ville = null;
    }
}
