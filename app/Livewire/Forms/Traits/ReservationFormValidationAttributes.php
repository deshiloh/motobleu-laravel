<?php

namespace App\Livewire\Forms\Traits;

trait ReservationFormValidationAttributes
{
    /**
     * Attributs personnalisés pour les messages d'erreur de validation
     *
     * Fournit des noms lisibles pour tous les champs du formulaire
     * afin d'améliorer l'expérience utilisateur.
     *
     * @return array<string, string> Mapping champ => nom lisible
     */
    protected function getValidationAttributes(): array
    {
        return array_merge(
            $this->getMainFieldsAttributes(),
            $this->getPassengerFieldsAttributes(),
            $this->getLocationFieldsAttributes(),
            $this->getBackReservationFieldsAttributes()
        );
    }

    /**
     * Attributs pour les champs principaux
     *
     * @return array<string, string>
     */
    private function getMainFieldsAttributes(): array
    {
        return [
            'userId' => 'secrétaire',
            'entrepriseId' => 'entreprise rattachée',
            'passengerId' => 'passager',
            'pickupDate' => 'date de prise en charge',
            'comment' => 'commentaire',
            'hasSteps' => 'destinations intermédiaires',
            'steps' => 'destinations intermédiaires',
        ];
    }

    /**
     * Attributs pour les champs passager
     *
     * @return array<string, string>
     */
    private function getPassengerFieldsAttributes(): array
    {
        return [
            'newPassager.nom' => 'nom et prénom',
            'newPassager.telephone' => 'téléphone de bureau',
            'newPassager.portable' => 'téléphone portable',
            'newPassager.email' => 'adresse email',
            'newPassager.cost_center_id' => 'cost center',
            'newPassager.type_facturation_id' => 'type de facturation',
        ];
    }

    /**
     * Attributs pour les champs de localisation
     *
     * @return array<string, string>
     */
    private function getLocationFieldsAttributes(): array
    {
        return [
            // Lieu de prise en charge
            'localisationFromId' => 'aéroports ou gares de départ',
            'pickupOrigin' => 'provenance / n°',
            'addressReservationFrom' => 'adresse de prise en charge',
            'newAdresseReservationFrom.adresse' => 'adresse de prise en charge',
            'newAdresseReservationFrom.adresseComplement' => 'adresse complémentaire de prise en charge',
            'newAdresseReservationFrom.codePostal' => 'code postal de prise en charge',
            'newAdresseReservationFrom.ville' => 'ville de prise en charge',

            // Lieu de destination
            'localisationToId' => 'aéroports ou gares de destination',
            'dropOffOrigin' => 'destination / n°',
            'addressReservationTo' => 'adresse de destination',
            'newAdresseReservationTo.adresse' => 'adresse de destination',
            'newAdresseReservationTo.adresse_complement' => 'adresse complémentaire de destination',
            'newAdresseReservationTo.codePostal' => 'code postal de destination',
            'newAdresseReservationTo.ville' => 'ville de destination',
        ];
    }

    /**
     * Attributs pour les champs de réservation retour
     *
     * @return array<string, string>
     */
    private function getBackReservationFieldsAttributes(): array
    {
        return [
            'reservationBack.pickupDate' => 'date de prise en charge retour',
            'reservationBack.comment' => 'commentaire retour',
            'reservationBack.hasSteps' => 'destinations intermédiaires retour',
            'reservationBack.steps' => 'destinations intermédiaires retour',
            'reservationBack.localisationFromId' => 'aéroports ou gares de départ retour',
            'reservationBack.pickupOrigin' => 'provenance / n° retour',
            'reservationBack.localisationToId' => 'aéroports ou gares de destination retour',
            'reservationBack.dropOffOrigin' => 'destination / n° retour',
            'reservationBack.adresseReservationFromId' => 'adresse de prise en charge retour',
            'reservationBack.adresseReservationToId' => 'adresse de destination retour',
        ];
    }
}