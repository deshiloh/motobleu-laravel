<?php

namespace App\Services;

use App\Enum\ReservationStatus;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Service pour la création de réservations
 *
 * Responsabilités:
 * - Création de réservations avec toutes les entités liées
 * - Gestion transactionnelle pour assurer la cohérence des données
 * - Logique de création des passagers et adresses si nécessaire
 */
class ReservationCreationService
{
    /**
     * Crée une réservation avec toutes ses entités liées
     * @throws Throwable
     */
    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            // 1. Créer ou récupérer le passager
            $passagerId = $this->handlePassenger($data);

            // 2. Créer les adresses si nécessaire
            $addressFromId = $this->handleFromAddress($data);
            $addressToId = $this->handleToAddress($data);

            // 3. Créer la réservation aller
            $reservation = $this->createReservationRecord($data, $passagerId, $addressFromId, $addressToId);

            // 4. Créer la réservation retour si nécessaire
            if (!empty($data['hasBack'])) {
                $this->createBackReservation($data, $passagerId, $reservation);
            }

            return $reservation;
        });
    }

    /**
     * Gère la création ou récupération du passager
     */
    private function handlePassenger(array $data): int
    {
        if ($data['passengerMode'] === ReservationService::EXIST_PASSAGER) {
            return $data['passengerId'];
        }

        // Créer un nouveau passager
        $passager = Passager::create([
            'nom' => $data['newPassager']['nom'],
            'telephone' => $data['newPassager']['telephone'] ?? null,
            'portable' => $data['newPassager']['portable'] ?? null,
            'email' => $data['newPassager']['email'],
            'user_id' => $data['userId'],
            'cost_center_id' => $data['newPassager']['cost_center_id'] ?? null,
            'type_facturation_id' => $data['newPassager']['type_facturation_id'] ?? null,
            'is_actif' => true,
        ]);

        return $passager->id;
    }

    /**
     * Gère la création de l'adresse de départ si nécessaire
     */
    private function handleFromAddress(array $data): ?int
    {
        if ($data['pickupMode'] === ReservationService::WITH_PLACE) {
            return null; // Utilise localisation_from_id
        }

        if ($data['pickupMode'] === ReservationService::WITH_ADRESSE) {
            return $data['addressReservationFrom'];
        }

        if ($data['pickupMode'] === ReservationService::WITH_NEW_ADRESSE) {
            $address = AdresseReservation::create([
                'adresse' => $data['newAdresseReservationFrom']['adresse'],
                'adresse_complement' => $data['newAdresseReservationFrom']['adresseComplement'] ?? null,
                'code_postal' => $data['newAdresseReservationFrom']['codePostal'],
                'ville' => $data['newAdresseReservationFrom']['ville'],
                'user_id' => $data['userId'],
                'is_actif' => true,
            ]);

            return $address->id;
        }

        return null;
    }

    /**
     * Gère la création de l'adresse de destination si nécessaire
     */
    private function handleToAddress(array $data): ?int
    {
        if ($data['dropMode'] === ReservationService::WITH_PLACE) {
            return null; // Utilise localisation_to_id
        }

        if ($data['dropMode'] === ReservationService::WITH_ADRESSE) {
            return $data['addressReservationTo'];
        }

        if ($data['dropMode'] === ReservationService::WITH_NEW_ADRESSE) {
            $address = AdresseReservation::create([
                'adresse' => $data['newAdresseReservationTo']['adresse'],
                'adresse_complement' => $data['newAdresseReservationTo']['adresseComplement'] ?? null,
                'code_postal' => $data['newAdresseReservationTo']['codePostal'],
                'ville' => $data['newAdresseReservationTo']['ville'],
                'user_id' => $data['userId'],
                'is_actif' => true,
            ]);

            return $address->id;
        }

        return null;
    }

    /**
     * Crée l'enregistrement de réservation
     */
    private function createReservationRecord(
        array $data,
        int $passagerId,
        ?int $addressFromId,
        ?int $addressToId
    ): Reservation {
        $reservationData = [
            'entreprise_id' => $data['entrepriseId'],
            'passager_id' => $passagerId,
            'pickup_date' => Carbon::createFromFormat('d/m/Y H:i', $data['pickupDate']),
            'statut' => ReservationStatus::Created,
            'has_steps' => $data['hasSteps'],
            'steps' => $data['hasSteps'] ? $data['steps'] : null,
            'comment' => $data['comment'],
        ];

        // Ajouter les localisations selon le mode
        if ($data['pickupMode'] === ReservationService::WITH_PLACE) {
            $reservationData['localisation_from_id'] = $data['localisationFromId'];
            $reservationData['pickup_origin'] = $data['pickupOrigin'] ?? null;
        } else {
            $reservationData['adresse_reservation_from_id'] = $addressFromId;
        }

        if ($data['dropMode'] === ReservationService::WITH_PLACE) {
            $reservationData['localisation_to_id'] = $data['localisationToId'];
            $reservationData['drop_off_origin'] = $data['dropOffOrigin'] ?? null;
        } else {
            $reservationData['adresse_reservation_to_id'] = $addressToId;
        }

        // Ajouter la commande si elle existe
        if (!empty($data['commande'])) {
            $reservationData['commande'] = $data['commande'];
        }

        return Reservation::create($reservationData);
    }

    /**
     * Crée la réservation retour
     * @throws Throwable
     */
    private function createBackReservation(array $data, int $passagerId, Reservation $goReservation): Reservation
    {
        $backData = $data['reservationBack'] ?? [];

        // Créer les adresses retour si nécessaire
        $backAddressFromId = $this->handleBackFromAddress($data);
        $backAddressToId = $this->handleBackToAddress($data);

        $reservationData = [
            'entreprise_id' => $data['entrepriseId'],
            'passager_id' => $passagerId,
            'pickup_date' => Carbon::create($backData['pickupDate']),
            'statut' => ReservationStatus::Created,
            'has_steps' => !empty($backData['hasSteps']),
            'steps' => !empty($backData['hasSteps']) ? ($backData['steps'] ?? null) : null,
            'comment' => $backData['comment'] ?? null,
            'reservation_id' => $goReservation->id,
        ];

        // Ajouter les localisations selon le mode
        if ($data['backPickupMode'] === ReservationService::WITH_PLACE) {
            $reservationData['localisation_from_id'] = $backData['localisationFromId'] ?? null;
            $reservationData['pickup_origin'] = $backData['pickupOrigin'] ?? null;
        } else {
            $reservationData['adresse_reservation_from_id'] = $backAddressFromId;
        }

        if ($data['backDropMode'] === ReservationService::WITH_PLACE) {
            $reservationData['localisation_to_id'] = $backData['localisationToId'] ?? null;
            $reservationData['drop_off_origin'] = $backData['dropOffOrigin'] ?? null;
        } else {
            $reservationData['adresse_reservation_to_id'] = $backAddressToId;
        }

        // Ajouter la commande si elle existe
        if (!empty($data['commande'])) {
            $reservationData['commande'] = $data['commande'];
        }

        return Reservation::create($reservationData);
    }

    /**
     * Gère la création de l'adresse de départ retour si nécessaire
     */
    private function handleBackFromAddress(array $data): ?int
    {
        if ($data['backPickupMode'] === ReservationService::WITH_PLACE) {
            return null;
        }

        if ($data['backPickupMode'] === ReservationService::WITH_ADRESSE) {
            return $data['reservationBack']['adresseReservationFromId'] ?? null;
        }

        if ($data['backPickupMode'] === ReservationService::WITH_NEW_ADRESSE) {
            $backFromData = $data['newAdresseReservationFromBack'] ?? [];

            if (!empty($backFromData['adresse'])) {
                $address = AdresseReservation::create([
                    'adresse' => $backFromData['adresse'],
                    'adresse_complement' => $backFromData['adresseComplement'] ?? null,
                    'code_postal' => $backFromData['codePostal'],
                    'ville' => $backFromData['ville'],
                    'user_id' => $data['userId'],
                    'is_actif' => true,
                ]);

                return $address->id;
            }
        }

        return null;
    }

    /**
     * Gère la création de l'adresse de destination retour si nécessaire
     */
    private function handleBackToAddress(array $data): ?int
    {
        if ($data['backDropMode'] === ReservationService::WITH_PLACE) {
            return null;
        }

        if ($data['backDropMode'] === ReservationService::WITH_ADRESSE) {
            return $data['reservationBack']['adresseReservationToId'] ?? null;
        }

        if ($data['backDropMode'] === ReservationService::WITH_NEW_ADRESSE) {
            $backToData = $data['newAdresseReservationToBack'] ?? [];

            if (!empty($backToData['adresse'])) {
                $address = AdresseReservation::create([
                    'adresse' => $backToData['adresse'],
                    'adresse_complement' => $backToData['adresseComplement'] ?? null,
                    'code_postal' => $backToData['codePostal'],
                    'ville' => $backToData['ville'],
                    'user_id' => $data['userId'],
                    'is_actif' => true,
                ]);

                return $address->id;
            }
        }

        return null;
    }
}
