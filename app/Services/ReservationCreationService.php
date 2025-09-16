<?php

namespace App\Services;

use App\Enum\ReservationStatus;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * @throws \Throwable
     */
    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            // 1. Créer ou récupérer le passager
            $passagerId = $this->handlePassenger($data);

            // 2. Créer les adresses si nécessaire
            $addressFromId = $this->handleFromAddress($data);
            $addressToId = $this->handleToAddress($data);

            // 3. Créer la réservation
            return $this->createReservationRecord($data, $passagerId, $addressFromId, $addressToId);
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
                'adresse_complement' => $data['newAdresseReservationFrom']['adresse_complement'] ?? null,
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
                'adresse_complement' => $data['newAdresseReservationTo']['adresse_complement'] ?? null,
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

        return Reservation::create($reservationData);
    }
}
