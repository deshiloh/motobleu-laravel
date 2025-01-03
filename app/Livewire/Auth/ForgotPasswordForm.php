<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use WireUi\Traits\Actions;

class ForgotPasswordForm extends Component
{
    use Actions;

    public string $email = '';

    public function render()
    {
        return view('livewire.auth.forgot-password-form')
            ->layout('components.guess-layout');
    }

    public function getRules(): array
    {
        return [
            'email' => 'required|email'
        ];
    }

    public function resetAction()
    {
        $this->validate();

        $status = Password::sendResetLink([
            'email' => $this->email
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->notification()->success(
                "Succés",
                __($status)
            );

            $this->fill(['email' => '']);
        } else {
            $this->notification()->error(
              "Une erreur est survenue",
                __($status)
            );
        }
    }
}
