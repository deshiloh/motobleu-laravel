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
            $this->notification([
                'title' => 'Opération réussite.',
                'description' => "Tout s'est bien passé.",
                'icon' => "success",
                'onClose' => [
                    'method' => 'redirectToList'
                ]
            ]);
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Erreur",
                description: "Une erreur est survenue veuillez réessayer ultérieurement."
            );

            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
            if (\App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error('Erreur pendant l\'attachement entreprises dans utilisateur', [
                    'exception' => $exception,
                    'user' => $this->user,
                    'entreprises' => $this->entreprises
                ]);
            }
        }
    }

    public function redirectToList()
    {
        return redirect()->to(route('admin.accounts.index'));
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
