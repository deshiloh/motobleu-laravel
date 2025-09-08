<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public $nom = '';
    public $prenom = '';
    public $email = '';
    public $telephone = '';
    public $adresse = '';
    public $adresse_bis = '';
    public $code_postal = '';
    public $ville = '';
    public $is_actif = true;

    public function setUser(User $user): void
    {
        $this->user = $user;
        
        $this->nom = $user->nom ?? '';
        $this->prenom = $user->prenom ?? '';
        $this->email = $user->email ?? '';
        $this->telephone = $user->telephone ?? '';
        $this->adresse = $user->adresse ?? '';
        $this->adresse_bis = $user->adresse_bis ?? '';
        $this->code_postal = $user->code_postal ?? '';
        $this->ville = $user->ville ?? '';
        $this->is_actif = $user->is_actif ?? true;
    }

    public function save(): User
    {
        $this->validate($this->rules());
        
        $data = collect($this->all())->except(['user'])->toArray();

        if ($this->user && $this->user->exists) {
            $this->user->update($data);
        } else {
            $this->user = User::create($data);
        }

        return $this->user;
    }

    public function rules(): array
    {
        $rules = [
            'nom' => 'required',
            'prenom' => 'required',
            'telephone' => 'nullable',
            'adresse' => 'nullable',
            'adresse_bis' => 'nullable',
            'code_postal' => 'nullable',
            'ville' => 'nullable',
            'is_actif' => 'boolean',
            'email' => 'required|email|unique:users,email'
        ];

        if ($this->user && $this->user->exists) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->user->id;
        }

        return $rules;
    }
}
