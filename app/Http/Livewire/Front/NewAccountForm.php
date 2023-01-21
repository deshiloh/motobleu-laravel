<?php

namespace App\Http\Livewire\Front;

use App\Mail\ConfirmationRegisterUserDemand;
use App\Mail\RegisterUserDemand;
use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;

class NewAccountForm extends Component
{
    use Actions;

    public User $user;
    public string $entrepriseName;
    protected array $rules = [
        'user.nom' => 'required',
        'user.prenom' => 'required',
        'user.email' => 'required|email',
        'user.telephone' => 'required',
        'user.adresse' => 'required',
        'user.adresse_bis' => 'nullable',
        'user.code_postal' => 'required',
        'user.ville' => 'required',
        'entrepriseName' => 'required'
    ];

    public function mount()
    {
        $this->user = new User();
    }

    public function render()
    {
        return view('livewire.front.new-account-form')
            ->layout('components.front-layout');
    }

    public function send()
    {
        $this->validate();

        $datas = [
            'nom' => $this->user->nom,
            'prenom' => $this->user->prenom,
            'email' => $this->user->email,
            'telephone' => $this->user->telephone,
            'adresse' => $this->user->adresse,
            'adresse_bis' => $this->user->adresse_bis,
            'code_postal' => $this->user->code_postal,
            'ville' => $this->user->ville,
            'entreprise_name' => $this->entrepriseName
        ];

        try {
            \Mail::to(config('mail.admin.address'))
                ->send(new RegisterUserDemand(
                    $datas
                ));

            \Mail::to($this->user->email)
                ->send(new ConfirmationRegisterUserDemand(
                    $datas
                ));

            $this->notification()->success(
                title: "Demande envoyée !",
                description: "Votre demande a bien été envoyée."
            );

            $this->user->nom = null;
            $this->user->prenom = null;
            $this->user->email = null;
            $this->user->telephone = null;
            $this->user->adresse = null;
            $this->user->adresse_bis = null;
            $this->user->code_postal = null;
            $this->user->ville = null;
            $this->entrepriseName = '';
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur est survenue",
                description: "Une erreur est survenue pendant l'enregistrement de la demande, veuillez essayer ultérieurement."
            );
        }
    }
}
