<?php

namespace App\Livewire\Forms;

use App\Livewire\Forms\Traits\ReservationFormValidationAttributes;
use App\Livewire\Forms\Traits\ReservationFormRules;
use App\Models\Passager;
use App\Services\ReservationCreationService;
use App\Services\ReservationService;
use App\Services\ReservationValidationService;
use Livewire\Form;

class FrontReservationForm extends Form
{
    use ReservationFormValidationAttributes;
    use ReservationFormRules;

    // === CONFIGURATION GÉNÉRALE ===
    public ?int $userId = null;
    public bool $hasBack = false;
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
    public bool $calendarPassengerInvitation = true;
    public bool $sendToPassenger = true;

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
     * Règles de validation pour les champs de base du formulaire (Front)
     * Adapté pour l'interface client - userId optionnel car géré par l'authentification
     *
     * @return array<string, string> Règles de validation de base
     */
    private function getBaseRules(): array
    {
        return [
            'hasBack' => 'boolean',
            'userId' => 'nullable|exists:users,id',
            'entrepriseId' => 'required|exists:entreprises,id',
            'pickupDate' => 'required|date_format:d/m/Y H:i',
            'hasSteps' => 'boolean',
            'comment' => 'nullable|string',
            'calendarPassengerInvitation' => 'boolean',
            'sendToPassenger' => 'boolean',
            'commande' => 'nullable|string',
        ];
    }

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
            ReservationService::WITH_PLACE => [
                'localisationFromId' => 'required|integer',
                'pickupOrigin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationFrom' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE =>
                $this->buildAddressRules('newAdresseReservationFrom'),
            default => []
        };
    }

    /**
     * Règles de validation pour le lieu d'arrivée
     *
     * @return array<string, string> Règles de validation pour l'arrivée
     */
    private function getDropOffRules(): array
    {
        return match ($this->dropMode) {
            ReservationService::WITH_PLACE => [
                'localisationToId' => 'required|integer',
                'dropOffOrigin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationTo' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE =>
                $this->buildAddressRules('newAdresseReservationTo'),
            default => []
        };
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
     * @throws \Throwable
     */
    public function save(): void
    {
        $reservationCreationService = new ReservationCreationService();
        $this->validate();
        $reservationCreationService->createReservation($this->all());
    }
}
