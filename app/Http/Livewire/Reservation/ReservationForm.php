<?php

namespace App\Http\Livewire\Reservation;

use App\Services\SentryService;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Validator;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use WireUi\Traits\Actions;

class ReservationForm extends Component
{
    use Actions;

    public int $passagerMode = ReservationService::EXIST_PASSAGER;
    public int $pickupMode = ReservationService::WITH_PLACE;
    public int $dropMode = ReservationService::WITH_PLACE;
    public int $backPickupMode = ReservationService::WITH_PLACE;
    public int $backDropMode = ReservationService::WITH_PLACE;
    public bool $hasBack = false;

    public $userId = '';

    public Reservation $reservation;
    public Reservation $reservation_back;

    public Passager $newPassager;

    public AdresseReservation $newAdresseReservationFrom;
    public AdresseReservation $newAdresseReservationTo;

    public AdresseReservation $newAdresseReservationFromBack;
    public AdresseReservation $newAdresseReservationToBack;

    public array $generatedRules = [];

    protected function rules(): array
    {
        ReservationService::generateDefaultRules($this->generatedRules);
        ReservationService::generatePassagerFromRules($this->generatedRules, $this->passagerMode);
        ReservationService::generateFromLocalisationRules($this->generatedRules, $this->pickupMode);
        ReservationService::generateToLocalisationRules($this->generatedRules, $this->dropMode);

        if ($this->hasBack) {

            ReservationService::generateFromLocalisationBackRules($this->generatedRules, $this->backPickupMode);
            ReservationService::generateToLocalisationBackRules($this->generatedRules, $this->backDropMode);
        }

        return $this->generatedRules;
    }

    protected $validationAttributes = [
        'reservation.passager_id' => 'passager',
        'reservation.pickup_date' => 'date de d??part',

        'reservation.localisation_from_id' => 'lieu de d??part',
        'reservation.pickup_origin' => 'provenance de d??part',
        'reservation.adresse_reservation_from_id' => 'adresse de d??part',
        'newAdresseReservationFrom.adresse' => 'adresse de la nouvelle adresse de d??part',
        'newAdresseReservationFrom.code_postal' => 'code postal de la nouvelle adresse de d??part',
        'newAdresseReservationFrom.ville' => 'ville de la nouvelle adresse de d??part',

        'reservation.localisation_to_id' => 'lieu d\'arriv??e',
        'reservation.drop_off_origin' => 'provenance d\'arriv??e',
        'reservation.adresse_reservation_to_id' => 'adresse d\'arriv??e',
        'newAdresseReservationTo.adresse' => 'adresse de la nouvelle adresse de d\'arriv??e',
        'newAdresseReservationTo.code_postal' => 'code postal de la nouvelle adresse d\'arriv??e',
        'newAdresseReservationTo.ville' => 'ville de la nouvelle adresse d\'arriv??e',

        'reservation_back.drop_date' => 'date de d??part',
        'reservation_back.localisation_from_id' => 'lieu de d??part',
        'reservation_back.pickup_origin' => 'provenance de d??part',
        'reservation_back.adresse_reservation_from_id' => 'adresse de d??part',
        'newAdresseReservationFromBack.adresse' => 'adresse de la nouvelle adresse de d??part',
        'newAdresseReservationFromBack.code_postal' => 'code postal de la nouvelle adresse de d??part',
        'newAdresseReservationFromBack.ville' => 'ville de la nouvelle adresse de d??part',

        'reservation_back.localisation_to_id' => 'lieu d\'arriv??e',
        'reservation_back.drop_off_origin' => 'provenance d\'arriv??e',
        'reservation_back.adresse_reservation_to_id' => 'adresse d\'arriv??e',
        'newAdresseReservationToBack.adresse' => 'adresse de la nouvelle adresse de d\'arriv??e',
        'newAdresseReservationToBack.code_postal' => 'code postal de la nouvelle adresse d\'arriv??e',
        'newAdresseReservationToBack.ville' => 'ville de la nouvelle adresse d\'arriv??e',
    ];

    public function mount(Reservation $reservation)
    {
        $this->reservation = $reservation;

        if ($this->reservation->exists) {
            $this->userId = $this->reservation->passager->user->id;

            if ($this->reservation->adresseReservationFrom()->exists()) {
                $this->pickupMode = ReservationService::WITH_ADRESSE;
            }

            if ($this->reservation->adresseReservationTo()->exists()) {
                $this->dropMode = ReservationService::WITH_ADRESSE;
            }

            if ($this->reservation->reservationBack()->exists()) {
                $this->hasBack = true;
                $this->reservation_back = Reservation::find($this->reservation->reservationBack->id);

                if ($this->reservation_back->adresseReservationFrom()->exists()) {
                    $this->backPickupMode = ReservationService::WITH_ADRESSE;
                }

                if ($this->reservation_back->adresseReservationTo()->exists()) {
                    $this->backPickupMode = ReservationService::WITH_ADRESSE;
                }
            }

        } else {
            $this->reservation_back = new Reservation();

            $this->newPassager = new Passager();

            $this->newAdresseReservationFrom = new AdresseReservation();
            $this->newAdresseReservationTo = new AdresseReservation();

            $this->newAdresseReservationFromBack = new AdresseReservation();
            $this->newAdresseReservationToBack = new AdresseReservation();

            $this->reservation->send_to_passager = true;
            $this->reservation->send_to_user = true;
        }

    }



    public function render()
    {
        return view('livewire.reservation.reversation-form')
            ->layout('components.admin-layout');
    }

    public function saveReservation()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                if ($this->hasBack &&
                    !empty($this->reservation->pickup_date) &&
                    $this->reservation->pickup_date->greaterThanOrEqualTo($this->reservation_back->pickup_date)
                ) {
                    $validator->errors()->add('reservation_back.pickup_date', "Date incorrect");
                }
            });
        })->validate();

        if ($this->passagerMode == ReservationService::NEW_PASSAGER) {
            $this->newPassager->user_id = $this->userId;
            $this->newPassager->save();
            $this->reservation->passager_id = $this->newPassager->id;
        }

        if ($this->pickupMode == ReservationService::WITH_NEW_ADRESSE) {
            $this->newAdresseReservationFrom->user_id = $this->reservation->passager->user->id;
            $this->newAdresseReservationFrom->save();
            $this->reservation->adresseReservationFrom()->associate($this->newAdresseReservationFrom);

        }

        if ($this->dropMode == ReservationService::WITH_NEW_ADRESSE) {
            $this->newAdresseReservationTo->user_id = $this->reservation->passager->user->id;
            $this->newAdresseReservationTo->save();
            $this->reservation->adresseReservationTo()->associate($this->newAdresseReservationTo);

        }

        if ($this->hasBack) {

            $this->reservation_back->passager_id = $this->reservation->passager_id;

            if ($this->backPickupMode == ReservationService::WITH_NEW_ADRESSE) {
                $this->newAdresseReservationFromBack->user_id = $this->reservation->passager->user->id;
                $this->newAdresseReservationFromBack->save();
                $this->reservation_back->adresseReservationFrom()->associate($this->newAdresseReservationFromBack);

            }

            if ($this->backDropMode == ReservationService::WITH_NEW_ADRESSE) {
                $this->newAdresseReservationToBack->user_id = $this->reservation->passager->user->id;
                $this->newAdresseReservationToBack->save();
                $this->reservation_back->adresseReservationTo()->associate($this->newAdresseReservationToBack);
            }

            $this->reservation->has_back = true;

            try {
                $this->reservation_back->save();
            } catch (\Exception $exception) {

                $this->notification()->error(
                    $title = 'Cr??ation impossible',
                    $description = 'Une erreur est survenue pendant la cr??ation de la r??servation'
                );

                Log::channel('logtail')->critical('Erreur pendant la cr??ation de la r??servation de retour', [
                    'erreur' => $exception->getMessage(),
                    'reservation' => $this->reservation,
                    'reservation_back' => $this->reservation_back
                ]);
            }
        }

        try {
            if ($this->hasBack) {
                $this->reservation->reservationBack()->associate($this->reservation_back->id);
            }

            $this->reservation->is_cancel = false;
            $this->reservation->is_confirmed = false;

            $this->reservation->save();

            session()->flash('success', 'Traitement de la r??servation trait?? avec succ??s.');
            redirect()
                ->to(route('admin.reservations.index'));

        } catch (\Exception $exception) {
            $this->notification()->error(
                $title = 'Cr??ation impossible',
                $description = 'Une erreur est survenue pendant la cr??ation de la r??servation'
            );

            if (App::environment(['local'])) {
                ray([
                    'reservation' => $this->reservation,
                    'reservation_back' => $this->reservation_back
                ])->exception($exception);
            }

            // TODO Sentry en production

            Log::channel('logtail')->critical('Erreur pendant la cr??ation de la r??servation', [
                'erreur' => $exception->getMessage(),
                'exception' => $exception,
                'reservation' => $this->reservation,
                'reservation_back' => $this->reservation_back
            ]);
        }
    }
}
