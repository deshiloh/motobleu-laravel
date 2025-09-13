<?php

namespace App\Services;

use App\Models\Reservation;
use app\Settings\BillSettings;

/**
 * Service ReservationService
 *
 * Ce service centralise la logique métier liée aux réservations,
 * notamment la génération dynamique des règles de validation
 * selon les différents modes et contextes.
 *
 * Responsabilités :
 * - Génération des règles de validation dynamiques
 * - Constantes pour les modes de sélection
 * - Logique de validation contextuelle (cost center, entreprises)
 * - Support des réservations aller-retour
 *
 * Modes supportés :
 * - Passagers : EXIST_PASSAGER, NEW_PASSAGER
 * - Adresses : WITH_PLACE, WITH_ADRESSE, WITH_NEW_ADRESSE
 *
 * @package App\Services
 * @author MotoBleue Team
 * @version 2.0
 */
class ReservationService
{
    // ===== CONSTANTES DE MODES =====

    /**
     * Mode passager : utiliser un passager existant
     */
    const EXIST_PASSAGER = 1;

    /**
     * Mode passager : créer un nouveau passager
     */
    const NEW_PASSAGER = 2;

    /**
     * Mode localisation : utiliser un lieu prédéfini (aéroport, gare, etc.)
     */
    const WITH_PLACE = 1;

    /**
     * Mode localisation : utiliser une adresse existante de l'utilisateur
     */
    const WITH_ADRESSE = 2;

    /**
     * Mode localisation : créer une nouvelle adresse
     */
    const WITH_NEW_ADRESSE = 3;

    // ===== MÉTHODES DE GÉNÉRATION DES RÈGLES =====

    /**
     * Génère les règles de validation de base pour toute réservation
     *
     * Ces règles sont communes à toutes les réservations, indépendamment
     * du mode ou du contexte.
     *
     * @param array<string, string> $rules Tableau des règles à modifier par référence
     * @return void
     */
    public static function generateDefaultRules(array &$rules): void
    {
        $rules =  [
            'hasBack' => 'bool',
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
     * Génère les règles de validation pour les passagers
     *
     * Cette méthode adapte les règles selon le mode de sélection du passager :
     * - EXIST_PASSAGER : valide l'ID du passager sélectionné
     * - NEW_PASSAGER : valide les champs de création + cost center si nécessaire
     *
     * Logique cost center :
     * Si l'entreprise est dans la liste des entreprises nécessitant un cost center,
     * les champs cost_center_id et type_facturation_id deviennent obligatoires.
     *
     * @param array<string, string> $rules Tableau des règles à modifier
     * @param int $mode Mode de sélection (EXIST_PASSAGER ou NEW_PASSAGER)
     * @param int|null $companySelected ID de l'entreprise pour validation cost center
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
        $rules['reservation_back.pickup_date'] = ['required', 'date', 'after:reservation.pickup_date'];

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
