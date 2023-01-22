<?php

namespace App\Http\Livewire\Settings;

use App\Mail\CancelReservationDemand;
use App\Mail\ConfirmationRegisterUserDemand;
use App\Mail\RegisterUserDemand;
use App\Mail\ReservationCanceled;
use App\Mail\ReservationConfirmed;
use App\Mail\ReservationCreated;
use App\Mail\UpdateReservationDemand;
use App\Mail\UserCreated;
use App\Models\Reservation;
use app\Settings\MailSettings;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use WireUi\Traits\Actions;

class EmailSettingsForm extends Component
{
    use Actions;
    public string $fromName = "";
    public string $fromAddress = "";
    public string $emailTest = "test@test.com";

    protected array $rules = [
        'fromName' => 'required',
        'fromAddress' => 'required|email'
    ];

    public function mount(MailSettings $mailSettings)
    {
        $mailSettings1 = $mailSettings;

        $this->fromName = $mailSettings1->from_name;
        $this->fromAddress = $mailSettings1->from_address;
    }

    public function render()
    {
        return view('livewire.settings.email-settings-form');
    }

    public function save(MailSettings $mailSettings)
    {
        $this->validate();

        try {
            $mailSettings->from_name = $this->fromName;
            $mailSettings->from_address = $this->fromAddress;
            $mailSettings->save();

            $this->notification()->success(
                title: "Opération réussite",
                description: "Sauvegarde des paramètres emails réussite."
            );
        } catch (\Exception $exception) {

        }

    }

    public function sendEmailTest($email)
    {
        $this->validate([
            'emailTest' => 'required|email'
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
                    ->send(new ReservationCreated(Reservation::find(1)));
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
            default :
                ray('NON');
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
