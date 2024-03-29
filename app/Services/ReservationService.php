<?php

namespace App\Services;

use App\Models\Reservation;
use app\Settings\BillSettings;

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
}
