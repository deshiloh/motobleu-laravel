<?php

namespace App\Traits;

use App\Mail\ReservationUpdated;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Services\ReservationService;
use app\Settings\BillSettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

trait WithReservationForm
{
    public int $passagerMode = ReservationService::EXIST_PASSAGER;
    public int $pickupMode = ReservationService::WITH_PLACE;
    public int $dropMode = ReservationService::WITH_PLACE;
    public int $backPickupMode = ReservationService::WITH_PLACE;
    public int $backDropMode = ReservationService::WITH_PLACE;
    public bool $hasBack = false;
    public bool $ardianPassengerCostFacError = false;

    public ?string $userId = '';

    public Reservation $reservation;
    public Reservation $reservation_back;

    public Passager $newPassager;
    public ?Passager $passengerInError = null;

    public AdresseReservation $newAdresseReservationFrom;
    public AdresseReservation $newAdresseReservationTo;

    public AdresseReservation $newAdresseReservationFromBack;
    public AdresseReservation $newAdresseReservationToBack;

    public array $generatedRules = [];

    protected function rules(): array
    {
        if ($this->ardianPassengerCostFacError) {
            return [
                'reservation.send_to_passager' => 'bool',
                'reservation.calendar_passager_invitation' => 'bool',
                'reservation.entreprise_id' => 'required',
                'passengerInError.cost_center_id' => 'required',
                'passengerInError.type_facturation_id' => 'required'
            ];
        }

        ReservationService::generateDefaultRules($this->generatedRules);
        ReservationService::generatePassagerFromRules($this->generatedRules, $this->passagerMode, $this->reservation->entreprise_id);
        ReservationService::generateFromLocalisationRules($this->generatedRules, $this->pickupMode);
        ReservationService::generateToLocalisationRules($this->generatedRules, $this->dropMode);

        if ($this->hasBack) {
            ReservationService::generateFromLocalisationBackRules($this->generatedRules, $this->backPickupMode);
            ReservationService::generateToLocalisationBackRules($this->generatedRules, $this->backDropMode);
        }

        return $this->generatedRules;
    }

    protected array $validationAttributes = [
        'reservation.passager_id' => 'passager',
        'reservation.pickup_date' => 'date de départ',
        'reservation.entreprise_id' => 'entreprise',

        'reservation.localisation_from_id' => 'lieu de départ',
        'reservation.pickup_origin' => 'provenance de départ',
        'reservation.adresse_reservation_from_id' => 'adresse de départ',
        'newAdresseReservationFrom.adresse' => 'adresse de la nouvelle adresse de départ',
        'newAdresseReservationFrom.code_postal' => 'code postal de la nouvelle adresse de départ',
        'newAdresseReservationFrom.ville' => 'ville de la nouvelle adresse de départ',

        'reservation.localisation_to_id' => 'lieu d\'arrivée',
        'reservation.drop_off_origin' => 'provenance d\'arrivée',
        'reservation.adresse_reservation_to_id' => 'adresse d\'arrivée',
        'newAdresseReservationTo.adresse' => 'adresse de la nouvelle adresse de d\'arrivée',
        'newAdresseReservationTo.code_postal' => 'code postal de la nouvelle adresse d\'arrivée',
        'newAdresseReservationTo.ville' => 'ville de la nouvelle adresse d\'arrivée',

        'reservation_back.drop_date' => 'date de départ',
        'reservation_back.localisation_from_id' => 'lieu de départ',
        'reservation_back.pickup_origin' => 'provenance de départ',
        'reservation_back.adresse_reservation_from_id' => 'adresse de départ',
        'newAdresseReservationFromBack.adresse' => 'adresse de la nouvelle adresse de départ',
        'newAdresseReservationFromBack.code_postal' => 'code postal de la nouvelle adresse de départ',
        'newAdresseReservationFromBack.ville' => 'ville de la nouvelle adresse de départ',

        'reservation_back.localisation_to_id' => 'lieu d\'arrivée',
        'reservation_back.drop_off_origin' => 'provenance d\'arrivée',
        'reservation_back.adresse_reservation_to_id' => 'adresse d\'arrivée',
        'newAdresseReservationToBack.adresse' => 'adresse de la nouvelle adresse de d\'arrivée',
        'newAdresseReservationToBack.code_postal' => 'code postal de la nouvelle adresse d\'arrivée',
        'newAdresseReservationToBack.ville' => 'ville de la nouvelle adresse d\'arrivée',
    ];

    private function defaultReset(): void
    {
        $this->reservation_back = new Reservation();

        $this->newPassager = new Passager();

        $this->newAdresseReservationFrom = new AdresseReservation();
        $this->newAdresseReservationTo = new AdresseReservation();

        $this->newAdresseReservationFromBack = new AdresseReservation();
        $this->newAdresseReservationToBack = new AdresseReservation();

        $this->reservation->send_to_passager = true;
        $this->reservation->calendar_passager_invitation = true;

        $this->reservation_back->send_to_passager = true;
        $this->reservation_back->calendar_passager_invitation = true;
    }

    public function updatedReservation(): void
    {
        $billSettings = \app(BillSettings::class);

        if (
            $this->reservation->passager_id &&
            in_array($this->reservation->entreprise_id, $billSettings->entreprises_cost_center_facturation)
        ) {
            $passenger = $this->reservation->passager;
            $this->passengerInError = $passenger;

            $this->ardianPassengerCostFacError = !$passenger->costCenter()->exists() &&
                !$passenger->typeFacturation()->exists();
        }
    }

    public function savePassenger(): void
    {
        $this->validate();

        $this->passengerInError->updateQuietly();

        $this->reservation->passager_id = $this->passengerInError->id;
        $this->ardianPassengerCostFacError = false;
    }

    /**
     * Création de la réservation
     * @param string $toRoute
     * @return void
     */
    private function createReservationWithRedirection(string $toRoute): void
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
            if ($this->reservation->passager()->exists()) {
                $this->reservation->passager()->disassociate();
            }

            $this->newPassager->user_id = $this->userId;
            $this->newPassager->save();
            $this->reservation->passager_id = $this->newPassager->id;
        }

        switch ($this->pickupMode) {
            case ReservationService::WITH_PLACE:
                if ($this->reservation->adresseReservationFrom()->exists()) {
                    $this->reservation->adresseReservationFrom()->disassociate();
                }
                break;
            case ReservationService::WITH_ADRESSE:
                if ($this->reservation->localisationFrom()->exists()) {
                    $this->reservation->localisationFrom()->disassociate();
                }
                break;
            case ReservationService::WITH_NEW_ADRESSE:
                if (null !== $this->reservation->localisationFrom()->exists()) {
                    $this->reservation->localisationFrom()->disassociate();
                }

                $this->newAdresseReservationFrom->user_id = $this->reservation->passager->user->id;
                $this->newAdresseReservationFrom->save();
                $this->reservation->adresseReservationFrom()->associate($this->newAdresseReservationFrom);

                break;
        }

        switch ($this->dropMode) {
            case ReservationService::WITH_PLACE:
                if ($this->reservation->adresseReservationTo()->exists()) {
                    $this->reservation->adresseReservationTo()->disassociate();
                }
                break;
            case ReservationService::WITH_ADRESSE:
                if ($this->reservation->localisationTo()->exists()) {
                    $this->reservation->localisationTo()->disassociate();
                }
                break;
            case ReservationService::WITH_NEW_ADRESSE:
                if (null !== $this->reservation->localisationTo()->exists()) {
                    $this->reservation->localisationTo()->disassociate();
                }

                $this->newAdresseReservationTo->user_id = $this->reservation->passager->user->id;
                $this->newAdresseReservationTo->save();
                $this->reservation->adresseReservationFrom()->associate($this->newAdresseReservationTo);

                break;
        }

        try {
            if ($this->reservation->exists && $this->reservation->isDirty()) {
                $contacts = [];

                if ($this->reservation->send_to_user) {
                    $contacts[] = $this->reservation->passager->user->email;
                }

                if ($this->reservation->send_to_passager) {
                    $contacts[] = $this->reservation->passager->email;
                }

                foreach ($contacts as $contact) {
                    \Mail::to($contact)
                        ->send(new ReservationUpdated($this->reservation));
                }

            }

            $this->reservation->save();

            session()->flash('success', 'Traitement de la réservation traité avec succés.');
            redirect()
                ->to($toRoute);

        } catch (\Exception $exception) {
            $this->notification()->error(
                $title = 'Création impossible',
                $description = 'Une erreur est survenue pendant la création de la réservation'
            );

            if (App::environment(['local'])) {
                ray([
                    'reservation' => $this->reservation,
                ])->exception($exception);
            }

            Log::channel("sentry")->error("Erreur pendant la création / édition d'une réservation retour", [
                'exception' => $exception,
                'reservation' => $this->reservation,
            ]);
        }

        if ($this->hasBack) {
            $this->reservation->has_back = true;

            $this->reservation_back->passager_id = $this->reservation->passager_id;
            $this->reservation_back->entreprise_id = $this->reservation->entreprise_id;
            $this->reservation_back->commande = $this->reservation->commande;

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

            $this->reservation_back->send_to_passager = $this->reservation->send_to_passager;
            $this->reservation_back->calendar_passager_invitation = $this->reservation->calendar_passager_invitation;

            try {
                $this->reservation_back->save();

                $this->reservation->reservationBack()
                    ->associate($this->reservation_back->id);

                $this->reservation->updateQuietly([
                    'reservation_id' => $this->reservation_back->id
                ]);
            } catch (\Exception $exception) {
                $this->notification()->error(
                    'Création impossible',
                    'Une erreur est survenue pendant la création de la réservation de retour'
                );

                if (App::environment(['prod', 'beta'])) {
                    Log::channel("sentry")->error("Erreur pendant la création / édition d'une réservation", [
                        'exception' => $exception,
                        'reservation' => $this->reservation,
                        'reservation_back' => $this->reservation_back
                    ]);
                }
            }
        }
    }
}
