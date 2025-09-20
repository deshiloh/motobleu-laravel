<?php

namespace App\Livewire\Forms;

use App\Models\Reservation;
use App\Services\ReservationCreationService;
use App\Services\ReservationService;
use App\Services\ReservationValidationService;
use Carbon\Carbon;
use Livewire\Form;
use Throwable;

/**
 * Formulaire de création de réservation pour l'interface d'administration
 *
 * Ce formulaire gère la création de réservations avec validation complète,
 * incluant la gestion des passagers, adresses, et réservations retour.
 * Applique les principes SOLID pour une maintenabilité optimale.
 *
 * @package App\Livewire\Forms
 * @author MotoBleue Team
 */
class AdminReservationForm extends Form
{
    // === CONFIGURATION GÉNÉRALE ===
    /** Indique si une réservation retour est demandée */
    public bool $hasBack = false;

    /** ID de l'utilisateur secrétaire créant la réservation */
    public ?int $userId = null;

    /** ID de l'entreprise pour laquelle la réservation est créée */
    public ?int $entrepriseId = null;

    /** Numéro de commande optionnel */
    public ?string $commande = null;

    // === GESTION DU PASSAGER ===
    /** Mode de sélection du passager (existant ou nouveau) */
    public int $passengerMode = ReservationService::EXIST_PASSAGER;

    /** ID du passager existant */
    public ?int $passengerId = null;

    /** Données pour créer un nouveau passager */
    public array $newPassager = [];

    // === INFORMATIONS TEMPORELLES ===
    /** Date et heure de prise en charge (format: d/m/Y H:i) */
    public ?string $pickupDate = null;

    /** Indique si la réservation a des étapes intermédiaires */
    public bool $hasSteps = false;

    /** Description des étapes intermédiaires */
    public ?string $steps = null;

    // === LIEU DE DÉPART ===
    /** Mode de sélection du lieu de départ */
    public int $pickupMode = ReservationService::WITH_PLACE;

    /** ID de la localisation de départ (aéroport/gare) */
    public ?int $localisationFromId = null;

    /** ID de l'adresse de départ existante */
    public ?int $addressReservationFrom = null;

    /** Données pour créer une nouvelle adresse de départ */
    public array $newAdresseReservationFrom = [];

    /** Origine spécifique (numéro de vol, etc.) */
    public ?string $pickupOrigin = null;

    // === LIEU D'ARRIVÉE ===
    /** Mode de sélection du lieu d'arrivée */
    public int $dropMode = ReservationService::WITH_PLACE;

    /** ID de la localisation d'arrivée (aéroport/gare) */
    public ?int $localisationToId = null;

    /** Destination spécifique (numéro de vol, etc.) */
    public ?string $dropOffOrigin = null;

    /** ID de l'adresse d'arrivée existante */
    public ?int $addressReservationTo = null;

    /** Données pour créer une nouvelle adresse d'arrivée */
    public array $newAdresseReservationTo = [];

    // === INFORMATIONS COMPLÉMENTAIRES ===
    /** Commentaire libre sur la réservation */
    public ?string $comment = null;

    // === RÉSERVATION RETOUR ===
    /** Données de la réservation retour */
    public array $reservationBack = [];

    /** Mode de sélection du lieu de départ retour */
    public int $backPickupMode = ReservationService::WITH_PLACE;

    /** Mode de sélection du lieu d'arrivée retour */
    public int $backDropMode = ReservationService::WITH_PLACE;

    /** Données pour créer une nouvelle adresse de départ retour */
    public array $newAdresseReservationFromBack = [];

    /** Données pour créer une nouvelle adresse d'arrivée retour */
    public array $newAdresseReservationToBack = [];

    // === NOTIFICATIONS ===
    /** Envoyer une invitation calendrier au passager */
    public bool $calendarPassagerInvitation = true;

    /** Envoyer une notification au passager */
    public bool $sendToPassager = true;

    /**
     * Définit les règles de validation complètes du formulaire
     *
     * Applique le principe de responsabilité unique (SRP) en déléguant
     * chaque type de validation à une méthode spécialisée.
     *
     * @return array<string, string> Règles de validation Laravel
     */
    protected function rules(): array
    {
        return array_merge(
            $this->getBaseRules(),
            $this->getPassengerRules(),
            $this->getLocationRules(),
            $this->getStepsRules(),
            $this->getBackReservationRules()
        );
    }

    /**
     * Règles de validation pour les champs de base du formulaire
     *
     * @return array<string, string> Règles de validation de base
     */
    private function getBaseRules(): array
    {
        return [
            'hasBack' => 'boolean',
            'userId' => 'required|exists:users,id',
            'entrepriseId' => 'required|exists:entreprises,id',
            'pickupDate' => 'required|date_format:d/m/Y H:i',
            'hasSteps' => 'boolean',
            'comment' => 'nullable|string',
            'calendarPassagerInvitation' => 'boolean',
            'sendToPassager' => 'boolean',
        ];
    }

    /**
     * Règles de validation pour la gestion des passagers
     *
     * Applique le principe ouvert/fermé (OCP) en utilisant des services
     * externes pour les règles métier spécialisées.
     *
     * @return array<string, string> Règles de validation pour les passagers
     */
    private function getPassengerRules(): array
    {
        return match ($this->passengerMode) {
            ReservationService::EXIST_PASSAGER => $this->getExistingPassengerRules(),
            ReservationService::NEW_PASSAGER => $this->getNewPassengerRules(),
            default => []
        };
    }

    /**
     * Règles pour un passager existant
     *
     * @return array<string, string>
     */
    private function getExistingPassengerRules(): array
    {
        return ['passengerId' => 'required|integer'];
    }

    /**
     * Règles pour un nouveau passager
     *
     * @return array<string, string>
     */
    private function getNewPassengerRules(): array
    {
        $rules = $this->buildNewPassengerBaseRules();

        if ($this->requiresCostCenterValidation()) {
            $rules = array_merge($rules, $this->buildCostCenterRules());
        }

        return $rules;
    }

    /**
     * Construit les règles de base pour un nouveau passager
     *
     * @return array<string, string>
     */
    private function buildNewPassengerBaseRules(): array
    {
        $commonRules = ReservationValidationService::getCommonPassengerRules();
        $rules = [];

        foreach ($commonRules as $field => $rule) {
            $rules["newPassager.{$field}"] = $rule;
        }

        return $rules;
    }

    /**
     * Vérifie si la validation du cost center est requise
     *
     * @return bool
     */
    private function requiresCostCenterValidation(): bool
    {
        return !is_null($this->entrepriseId) &&
               ReservationValidationService::requiresCostCenter($this->entrepriseId);
    }

    /**
     * Construit les règles pour le cost center
     *
     * @return array<string, string>
     */
    private function buildCostCenterRules(): array
    {
        $costCenterRules = ReservationValidationService::getPassengerCorrectionRules();
        $rules = [];

        foreach ($costCenterRules as $field => $rule) {
            $rules["newPassager.{$field}"] = $rule;
        }

        return $rules;
    }

    /**
     * Règles de validation pour les localisations (départ et arrivée)
     *
     * Combine les règles de départ et d'arrivée en respectant le principe
     * de responsabilité unique.
     *
     * @return array<string, string> Règles de validation pour les localisations
     */
    private function getLocationRules(): array
    {
        return array_merge(
            $this->getPickupRules(),
            $this->getDropOffRules()
        );
    }

    /**
     * Règles de validation pour le lieu de départ
     *
     * Utilise le pattern Strategy via match expression pour gérer
     * les différents modes de sélection du lieu de départ.
     *
     * @return array<string, string> Règles de validation pour le départ
     */
    private function getPickupRules(): array
    {
        return match ($this->pickupMode) {
            ReservationService::WITH_PLACE => $this->getPlacePickupRules(),
            ReservationService::WITH_ADRESSE => $this->getAddressPickupRules(),
            ReservationService::WITH_NEW_ADRESSE => $this->getNewAddressPickupRules(),
            default => []
        };
    }

    /**
     * Règles pour départ depuis une localisation (aéroport/gare)
     *
     * @return array<string, string>
     */
    private function getPlacePickupRules(): array
    {
        return [
            'localisationFromId' => 'required|integer',
            'pickupOrigin' => 'nullable|string'
        ];
    }

    /**
     * Règles pour départ depuis une adresse existante
     *
     * @return array<string, string>
     */
    private function getAddressPickupRules(): array
    {
        return ['addressReservationFrom' => 'required|integer'];
    }

    /**
     * Règles pour départ depuis une nouvelle adresse
     *
     * @return array<string, string>
     */
    private function getNewAddressPickupRules(): array
    {
        return $this->buildAddressRules('newAdresseReservationFrom');
    }

    /**
     * Règles de validation pour le lieu d'arrivée
     *
     * Miroir de getPickupRules() avec une logique similaire pour l'arrivée.
     *
     * @return array<string, string> Règles de validation pour l'arrivée
     */
    private function getDropOffRules(): array
    {
        return match ($this->dropMode) {
            ReservationService::WITH_PLACE => $this->getPlaceDropOffRules(),
            ReservationService::WITH_ADRESSE => $this->getAddressDropOffRules(),
            ReservationService::WITH_NEW_ADRESSE => $this->getNewAddressDropOffRules(),
            default => []
        };
    }

    /**
     * Règles pour arrivée vers une localisation (aéroport/gare)
     *
     * @return array<string, string>
     */
    private function getPlaceDropOffRules(): array
    {
        return [
            'localisationToId' => 'required|integer',
            'dropOffOrigin' => 'nullable|string'
        ];
    }

    /**
     * Règles pour arrivée vers une adresse existante
     *
     * @return array<string, string>
     */
    private function getAddressDropOffRules(): array
    {
        return ['addressReservationTo' => 'required|integer'];
    }

    /**
     * Règles pour arrivée vers une nouvelle adresse
     *
     * @return array<string, string>
     */
    private function getNewAddressDropOffRules(): array
    {
        return $this->buildAddressRules('newAdresseReservationTo');
    }

    /**
     * Construit les règles de validation pour une nouvelle adresse
     *
     * Méthode utilitaire qui évite la duplication de code entre
     * les adresses de départ et d'arrivée.
     *
     * @param string $prefix Préfixe pour les champs d'adresse
     * @return array<string, string>
     */
    private function buildAddressRules(string $prefix): array
    {
        $commonRules = ReservationValidationService::getCommonAddressRules();

        return array_combine(
            array_map(fn($key) => "{$prefix}.{$key}", array_keys($commonRules)),
            array_values($commonRules)
        );
    }

    /**
     * Règles de validation pour les étapes intermédiaires
     *
     * Simple et direct selon le principe KISS.
     *
     * @return array<string, string> Règles pour les étapes
     */
    private function getStepsRules(): array
    {
        return [
            'steps' => $this->hasSteps ? 'required|string' : 'nullable|string'
        ];
    }

    /**
     * Règles de validation pour la réservation retour
     *
     * Gère la complexité de validation des réservations retour en
     * décomposant en méthodes spécialisées.
     *
     * @return array<string, string> Règles de validation pour le retour
     */
    private function getBackReservationRules(): array
    {
        if (!$this->hasBack) {
            return [];
        }

        return array_merge(
            $this->getBackBaseRules(),
            $this->getBackPickupRules(),
            $this->getBackDropOffRules()
        );
    }

    /**
     * Règles de base pour la réservation retour
     *
     * @return array<string, string>
     */
    private function getBackBaseRules(): array
    {
        return [
            'reservationBack.pickupDate' => $this->buildBackDateRule(),
            'reservationBack.comment' => 'nullable|string',
            'reservationBack.hasSteps' => 'boolean',
            'reservationBack.steps' => 'nullable|string',
        ];
    }

    /**
     * Construit la règle de validation pour la date de retour
     *
     * La date de retour doit être postérieure à la date aller.
     *
     * @return string Règle de validation Laravel
     */
    private function buildBackDateRule(): string
    {
        if (!$this->pickupDate) {
            return 'required|date';
        }

        $parsedDate = Carbon::createFromFormat('d/m/Y H:i', $this->pickupDate);
        return 'required|date|after:' . $parsedDate->format('Y-m-d H:i:s');
    }

    /**
     * Règles pour le lieu de départ de la réservation retour
     *
     * @return array<string, string>
     */
    private function getBackPickupRules(): array
    {
        return match ($this->backPickupMode) {
            ReservationService::WITH_PLACE => [
                'reservationBack.localisationFromId' => 'required|integer',
                'reservationBack.pickupOrigin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'reservationBack.adresseReservationFromId' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE =>
                $this->buildAddressRules('newAdresseReservationFromBack'),
            default => []
        };
    }

    /**
     * Règles pour le lieu d'arrivée de la réservation retour
     *
     * @return array<string, string>
     */
    private function getBackDropOffRules(): array
    {
        return match ($this->backDropMode) {
            ReservationService::WITH_PLACE => [
                'reservationBack.localisationToId' => 'required|integer',
                'reservationBack.dropOffOrigin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'reservationBack.adresseReservationToId' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE =>
                $this->buildAddressRules('newAdresseReservationToBack'),
            default => []
        };
    }

    /**
     * Attributs personnalisés pour les messages d'erreur de validation
     *
     * Fournit des noms lisibles pour tous les champs du formulaire
     * afin d'améliorer l'expérience utilisateur.
     *
     * @return array<string, string> Mapping champ => nom lisible
     */
    protected function validationAttributes(): array
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

    /**
     * Crée une réservation sans validation préalable
     *
     * Méthode d'interface avec le service de création de réservation.
     * Applique le principe d'inversion de dépendance (DIP) en déléguant
     * la logique métier au service spécialisé.
     *
     * @return Reservation La réservation créée
     * @throws Throwable En cas d'erreur lors de la création
     */
    public function createReservationWithoutValidation(): Reservation
    {
        $reservationCreationService = new ReservationCreationService();

        return $reservationCreationService->createReservation($this->all());
    }
}
