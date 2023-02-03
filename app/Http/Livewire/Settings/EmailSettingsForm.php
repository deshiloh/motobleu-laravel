<?php

namespace App\Http\Livewire\Settings;

use App\Mail\CancelReservationDemand;
use App\Mail\ConfirmationRegisterUserDemand;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Mail\RegisterUserDemand;
use App\Mail\ReservationCanceled;
use App\Mail\ReservationConfirmed;
use App\Mail\ReservationCreated;
use App\Mail\ReservationUpdated;
use App\Mail\UpdateReservationDemand;
use App\Mail\UserCreated;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use WireUi\Traits\Actions;

class EmailSettingsForm extends Component
{
    use Actions;
    public string $emailTest = "";
    public bool $adminMode = false;

    public function mount()
    {
        $this->emailTest = config('mail.admin.address');
    }

    public function render()
    {
        return view('livewire.settings.email-settings-form');
    }

    public function sendEmailTest($email)
    {
        $this->validate([
            'emailTest' => 'required|email',
            'adminMode' => 'boolean'
        ]);

        switch ($email) {
            case $this->cleanClassName(RegisterUserDemand::class):
                \Mail::to($this->emailTest)
                    ->send(new RegisterUserDemand([
                        'nom' => 'test',
                        'prenom' => 'test',
                        'email' => 'email@address.com',
                        'telephone' => '0404040404',
                        'adresse' => 'Adresse postale',
                        'adresse_bis' => 'Adresse complémentaire',
                        'code_postal' => '34000',
                        'ville' => 'Montpellier',
                        'entreprise_name' => 'Nom entreprise',
                    ]));
                break;
            case $this->cleanClassName(UserCreated::class):
                \Mail::to($this->emailTest)
                    ->send(new UserCreated(\Auth::user()));
                break;
            case $this->cleanClassName(ConfirmationRegisterUserDemand::class):
                \Mail::to($this->emailTest)
                    ->send(new ConfirmationRegisterUserDemand());
                break;
            case $this->cleanClassName(ReservationCreated::class) :
                \Mail::to($this->emailTest)
                    ->send(new ReservationCreated(Reservation::find(1), $this->adminMode));
                break;
            case $this->cleanClassName(ReservationUpdated::class) :
                \Mail::to($this->emailTest)
                    ->send(new ReservationUpdated(Reservation::find(1)));
                break;
            case $this->cleanClassName(ReservationConfirmed::class) :
                Mail::to($this->emailTest)
                    ->send(new ReservationConfirmed(Reservation::find(1)));
                break;
            case $this->cleanClassName(ReservationCanceled::class) :
                Mail::to($this->emailTest)
                    ->send(new ReservationCanceled(Reservation::find(1)));
                break;
            case $this->cleanClassName(CancelReservationDemand::class) :
                Mail::to($this->emailTest)
                    ->send(new CancelReservationDemand(Reservation::find(1), \Auth::user()));
                break;
            case $this->cleanClassName(UpdateReservationDemand::class) :
                Mail::to($this->emailTest)
                    ->send(new UpdateReservationDemand(Reservation::find(1), "Ici sera affiché le contenu du message renseigné par l'assistante."));
                break;
            case $this->cleanClassName(PiloteAttached::class) :
                Mail::to($this->emailTest)
                    ->send(new PiloteAttached(Reservation::find(1)));
                break;
            case $this->cleanClassName(PiloteDetached::class) :
                Mail::to($this->emailTest)
                    ->send(new PiloteDetached(Reservation::find(1)));
                break;
        }
    }

    /**
     * Permet d'enlever les \ du nom de la class
     * @param string $className
     * @return string
     */
    private function cleanClassName(string $className): string
    {
        return preg_replace('/\\\\/', '', $className);
    }
}
