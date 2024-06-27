<?php

namespace App\Services;

use App\Enum\ReservationStatus;
use App\Events\ReservationCanceled;
use App\Events\ReservationCanceledPay;
use App\Events\ReservationConfirmed;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Mail\ReservationUpdated;
use App\Models\Pilote;
use App\Models\Reservation;
use app\Settings\BillSettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationService
{
    const EXIST_PASSAGER = 1;
    const NEW_PASSAGER = 2;

    const WITH_PLACE = 1;
    const WITH_ADRESSE = 2;
    const WITH_NEW_ADRESSE = 3;

    /**
     * @param array $rules
     * @return void
     */
    public static function generateDefaultRules(array &$rules): void
    {
        $rules =  [
            'userId' => 'required',
            'reservation.entreprise_id' => 'required',
            'reservation.pickup_date' => 'required',
            'reservation.commande' => 'nullable',
            'reservation.send_to_passager' => 'bool',
            'reservation.calendar_passager_invitation' => 'bool',
            'reservation.comment' => 'nullable',
            'reservation.has_steps' => 'bool',
            'reservation.steps' => 'nullable',
            'reservation_back.has_steps' => 'bool',
            'reservation_back.steps' => 'nullable',
            'reservation_back.comment' => 'nullable',
        ];
    }

    /**
     * @param array $rules
     * @param int $mode
     * @param int|null $companySelected $
     * @return void
     */
    public static function generatePassagerFromRules(array &$rules, int $mode, ?int $companySelected): void
    {
        if ($mode == ReservationService::EXIST_PASSAGER) {
            $rules['reservation.passager_id'] = 'required';
        }

        if ($mode == ReservationService::NEW_PASSAGER) {
            $rules['newPassager.nom'] = 'required';
            $rules['newPassager.telephone'] = 'nullable';
            $rules['newPassager.email'] = 'required|email';
            $rules['newPassager.portable'] = 'required';
            $rules['userId'] = 'required';

            if (!is_null($companySelected) && in_array($companySelected, app(BillSettings::class)->entreprises_cost_center_facturation)) {
                $rules['newPassager.cost_center_id'] = 'required';
                $rules['newPassager.type_facturation_id'] = 'required';
            }
        }
    }

    /**
     * @param array $rules
     * @param int $mode
     * @param Reservation $reservation
     * @return void
     */
    public static function generateFromLocalisationRules(array &$rules, int $mode, Reservation $reservation)
    {
        if ($mode == ReservationService::WITH_PLACE) {
            $rules['reservation.localisation_from_id'] = 'required';
            $rules['reservation.pickup_origin'] = 'nullable';
        }

        if ($mode == ReservationService::WITH_ADRESSE &&
            $reservation->exists() &&
            $reservation->adresse_reservation_from_id === null
        ) {
            $rules['addressReservationFrom'] = 'required';
        }

        if ($mode == ReservationService::WITH_NEW_ADRESSE) {
            $rules['newAdresseReservationFrom.adresse'] = 'required';
            $rules['newAdresseReservationFrom.adresse_complement'] = 'nullable';
            $rules['newAdresseReservationFrom.code_postal'] = 'required';
            $rules['newAdresseReservationFrom.ville'] = 'required';
        }
    }

    /**
     * @param array $rules
     * @param int $mode
     * @param Reservation $reservation
     * @return void
     */
    public static function generateToLocalisationRules(array &$rules, int $mode, Reservation $reservation)
    {
        if ($mode == ReservationService::WITH_PLACE) {
            $rules['reservation.localisation_to_id'] = 'required';
            $rules['reservation.drop_off_origin'] = 'nullable';
        }

        if ($mode == ReservationService::WITH_ADRESSE &&
            $reservation->exists() &&
            $reservation->adresse_reservation_to_id === null
        ) {
            $rules['addressReservationTo'] = 'required';
        }

        if ($mode == ReservationService::WITH_NEW_ADRESSE) {
            $rules['newAdresseReservationTo.adresse'] = 'required';
            $rules['newAdresseReservationTo.adresse_complement'] = 'nullable';
            $rules['newAdresseReservationTo.code_postal'] = 'required';
            $rules['newAdresseReservationTo.ville'] = 'required';
        }
    }

    /**
     * @param array $rules
     * @param int $mode
     * @return void
     */
    public static function generateFromLocalisationBackRules(array &$rules, int $mode)
    {
        $rules['reservation_back.pickup_date'] = 'required';

        if ($mode == ReservationService::WITH_PLACE) {
            $rules['reservation_back.localisation_from_id'] = 'required';
            $rules['reservation_back.pickup_origin'] = 'nullable';
        }

        if ($mode == ReservationService::WITH_ADRESSE) {
            $rules['reservation_back.adresse_reservation_from_id'] = 'required';
        }

        if ($mode == ReservationService::WITH_NEW_ADRESSE) {
            $rules['newAdresseReservationFromBack.adresse'] = 'required';
            $rules['newAdresseReservationFromBack.adresse_complement'] = 'nullable';
            $rules['newAdresseReservationFromBack.code_postal'] = 'required';
            $rules['newAdresseReservationFromBack.ville'] = 'required';
        }
    }

    /**
     * @param array $rules
     * @param int $mode
     * @return void
     */
    public static function generateToLocalisationBackRules(array &$rules, int $mode)
    {
        if ($mode == ReservationService::WITH_PLACE) {
            $rules['reservation_back.localisation_to_id'] = 'required';
            $rules['reservation_back.drop_off_origin'] = 'nullable';
        }

        if ($mode == ReservationService::WITH_ADRESSE) {
            $rules['reservation_back.adresse_reservation_to_id'] = 'required';
        }

        if ($mode == ReservationService::WITH_NEW_ADRESSE) {
            $rules['newAdresseReservationToBack.adresse'] = 'required';
            $rules['newAdresseReservationToBack.adresse_complement'] = 'nullable';
            $rules['newAdresseReservationToBack.code_postal'] = 'required';
            $rules['newAdresseReservationToBack.ville'] = 'required';
        }
    }

    /**
     * Permet de passer une réservation en statut : annulée, mais facturable
     * @param Reservation $reservation
     * @return Reservation
     */
    public function updateCancelledBilledStatut(Reservation $reservation): Reservation
    {
        $reservation->update([
            'statut' => ReservationStatus::CanceledToPay->value
        ]);

        $reservation->refresh();

        ReservationCanceledPay::dispatch($reservation);

        return $reservation;
    }

    /**
     * Permet de mettre à jour le pilote de la réservation
     * @param Reservation $reservation
     * @param Pilote $newPilote
     * @param float $encompte
     * @param float $encaisse
     * @param string|null $commentPilote
     * @return Reservation
     */
    public function updatePilote(
        Reservation $reservation,
        Pilote $newPilote,
        float $encompte,
        float $encaisse,
        ?string $commentPilote
    ): Reservation {
        $currentPilote = $reservation->pilote;
        $reservation->pilote()->associate($newPilote);
        $reservation->encaisse_pilote = $encaisse;
        $reservation->encompte_pilote = $encompte;
        $reservation->comment_pilote = $commentPilote;
        $reservation->save();

        try {
            \Mail::to($currentPilote->email)->send(new PiloteDetached($reservation));
        } catch (\Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('Erreur pendant l\'envoi de l\'email à l\'ancien pilote', [
                    'exception' => $exception,
                    'reservation' => $reservation,
                    'pilote' => $currentPilote,
                ]);
            }
        }

        try {
            \Mail::to($newPilote->email)->send(new PiloteAttached($reservation));
        } catch (\Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('Erreur pendant l\'envoi de l\'email au nouveau pilote', [
                    'exception' => $exception,
                    'reservation' => $reservation,
                    'pilote' => $newPilote,
                ]);
            }
        }

        return $reservation;
    }

    /**
     * Permet de confirmer une réservation
     * @param Reservation $reservation
     * @param Pilote $pilote
     * @param int $encompte
     * @param int $encaisse
     * @param string|null $commentPilote
     * @param string $message
     * @return Reservation
     */
    public function confirmReservation(
        Reservation $reservation,
        Pilote $pilote,
        int $encompte,
        int $encaisse,
        ?string $commentPilote,
        string $message
    ): Reservation {
        $reservation->statut = ReservationStatus::Confirmed->value;
        $reservation->encaisse_pilote = $encaisse;
        $reservation->encompte_pilote = $encompte;
        $reservation->comment_pilote = $commentPilote;

        $reservation->pilote()->associate($pilote);

        $reservation->update();
        $reservation->refresh();

        try {
            \Mail::to($reservation->pilote->email)
                ->send(new PiloteAttached($reservation));
        } catch (\Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant l'envoi de l'email au pilote", [
                    'exception' => $exception,
                    'reservation' => $reservation,
                    'pilote' => $pilote,
                ]);
            }
        }

        ReservationConfirmed::dispatch($reservation, $message ?? '');

        return $reservation;
    }

    /**
     * Annule une réservation
     * @param Reservation $reservation
     * @return Reservation
     */
    public function cancelReservation(Reservation $reservation): Reservation
    {
        $facture = $reservation->facture;
        $pilote = $reservation->pilote;

        $reservation->statut = ReservationStatus::Canceled->value;
        $reservation->encompte_pilote = null;
        $reservation->encaisse_pilote = null;

        if ($facture !== null) {
            if ($facture->reservations->count() > 1) {
                $reservation->facture()->disassociate();
                $reservation->tarif = null;
                $reservation->majoration = null;
                $reservation->complement = null;
            } else {
                $reservation->statut = ReservationStatus::Billed->value;
                $reservation->tarif = 0;
                $reservation->majoration = 0;
                $reservation->complement = 0;

                $facture->montant_ttc = 0;
                $facture->update();

                $facture->refresh();
            }
        }

        if ($reservation->pilote != null) {
            $reservation->pilote()->disassociate();
            Mail::to($pilote->email)
                ->send(new PiloteDetached($reservation));
        }

        $reservation->update();
        $reservation->refresh();

        ReservationCanceled::dispatch($reservation);

        return $reservation;
    }

    /**
     * Mettre à jour la réservation
     * @param Reservation $reservation
     * @param array $datas
     * @return Reservation
     */
    public function updateReservation(Reservation $reservation, array $datas): Reservation
    {
        $contacts = [];

        $reservation->
        $reservation->entreprise_id = $datas['company_id'];

        foreach ($contacts as $contact) {
            Mail::to($contact)->send(new ReservationUpdated($reservation));
        }

        return $reservation;
    }
}
