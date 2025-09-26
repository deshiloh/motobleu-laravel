<?php

namespace App\Livewire\Forms;

use App\Livewire\Forms\Traits\ReservationFormValidationAttributes;
use App\Livewire\Forms\Traits\ReservationFormRules;
use App\Services\ReservationCreationService;
use App\Services\ReservationService;
use Livewire\Form;
use Throwable;

/**
 * Classe de base pour les formulaires de réservation
 *
 * Factorisation du code commun entre AdminReservationForm et FrontReservationForm
 * selon les principes DRY et SOLID.
 *
 * @package App\Livewire\Forms
 * @author MotoBleue Team
 */
abstract class BaseReservationForm extends Form
{
    use ReservationFormValidationAttributes;
    use ReservationFormRules;

    // === CONFIGURATION GÉNÉRALE ===
    public bool $hasBack = false;
    public ?int $userId = null;
    public ?int $entrepriseId = null;
    public ?string $commande = null;

    // === GESTION DU PASSAGER ===
    public int $passengerMode = ReservationService::EXIST_PASSAGER;
    public ?int $passengerId = null;
    public array $newPassager = [];

    // === INFORMATIONS TEMPORELLES ===
    public ?string $pickupDate = null;
    public bool $hasSteps = false;
    public ?string $steps = null;

    // === LIEU DE DÉPART ===
    public int $pickupMode = ReservationService::WITH_PLACE;
    public ?int $localisationFromId = null;
    public ?int $addressReservationFrom = null;
    public array $newAdresseReservationFrom = [];
    public ?string $pickupOrigin = null;

    // === LIEU D'ARRIVÉE ===
    public int $dropMode = ReservationService::WITH_PLACE;
    public ?int $localisationToId = null;
    public ?string $dropOffOrigin = null;
    public ?int $addressReservationTo = null;
    public array $newAdresseReservationTo = [];

    // === INFORMATIONS COMPLÉMENTAIRES ===
    public ?string $comment = null;

    // === RÉSERVATION RETOUR ===
    public array $reservationBack = [];
    public int $backPickupMode = ReservationService::WITH_PLACE;
    public int $backDropMode = ReservationService::WITH_PLACE;
    public array $newAdresseReservationFromBack = [];
    public array $newAdresseReservationToBack = [];

    // === NOTIFICATIONS ===
    public bool $calendarPassagerInvitation = true;
    public bool $sendToPassager = true;

    /**
     * Définit les règles de validation complètes du formulaire
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
     * À redéfinir dans les classes enfants selon le contexte (Admin vs Front)
     *
     * @return array<string, string> Règles de validation de base
     */
    abstract protected function getBaseRules(): array;

    /**
     * Règles de validation pour les localisations (départ et arrivée)
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
     * Règles de validation pour les étapes intermédiaires
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
     * @return array<string, string> Mapping champ => nom lisible
     */
    protected function validationAttributes(): array
    {
        return $this->getValidationAttributes();
    }

    /**
     * Crée une réservation avec validation
     *
     * @return void
     * @throws Throwable En cas d'erreur lors de la création
     */
    protected function createReservation(): void
    {
        $reservationCreationService = new ReservationCreationService();
        $reservationCreationService->createReservation($this->all());
    }
}