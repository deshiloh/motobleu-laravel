<?php

namespace App\Http\Livewire\Account;

use App\Models\Entreprise;
use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;

class EntrepriseForm extends Component
{
    use Actions;

    public User $user;
    public array $entreprises = [];
    public array $exclude = [];

    protected array $rules = [
        'entreprises' => 'required'
    ];

    public function mount(User $account)
    {
        ray($this->exclude);
        $this->user = $account;
        $this->exclude = $this->user->entreprises()->pluck('id')->toArray();
    }

    public function render()
    {
        return view('livewire.account.entreprise-form')
            ->layout('components.layout');
    }

    public function save()
    {
        $this->validate();

        try {
            $this->user->entreprises()->attach($this->entreprises);
            $this->refreshData();
            $this->reset('entreprises');
            $this->notification()->success(
                title: "Opération réussite.",
                description: "Tout s'est bien passé."
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Erreur",
                description: "Une erreur est survenue veuillez réessayer ultérieurement."
            );

            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
            // TODO SENTRY
        }
    }

    public function refreshData()
    {
        $this->exclude = $this->user->entreprises()->pluck('id')->toArray();
    }

    public function detach(Entreprise $entreprise)
    {
        $this->user->entreprises()->detach($entreprise->id);
        $this->refreshData();
    }
}
