<?php

namespace App\Livewire\Forms;

use App\Models\Passager;
use App\Services\ReservationService;
use App\Services\ReservationValidationService;
use Livewire\Form;

/**
 * Classe AdminReservationForm - Livewire Form pour l'interface Admin
 *
 * Cette classe représente le formulaire Livewire pour la création et modification
 * de réservations dans l'interface d'administration. Elle inclut toutes les
 * fonctionnalités avancées nécessaires aux administrateurs.
 *
 * Fonctionnalités spécifiques admin :
 * - Sélection de secrétaire via userId
 * - Validation cost center basée sur l'entreprise de la secrétaire
 * - Accès à toutes les entreprises et passagers
 * - Gestion complète des erreurs et validations
 *
 * @package App\Livewire\Forms
 * @author MotoBleue Team
 * @version 2.0
 */
class AdminReservationFormBack extends Form
{
    // ===== SÉLECTION UTILISATEUR ET MODES =====

    /**
     * ID de l'utilisateur sélectionné (secrétaire en contexte admin)
     * OBLIGATOIRE en interface admin pour déterminer l'entreprise
     *
     * @var string|null
     */
    public ?string $userId = '';

    /**
     * Mode de sélection du passager
     * - EXIST_PASSAGER : sélectionner un passager existant
     * - NEW_PASSAGER : créer un nouveau passager
     *
     * @var int
     */
    public int $passagerMode = ReservationService::EXIST_PASSAGER;

    /**
     * Mode de sélection du lieu de départ
     * - WITH_PLACE : lieu prédéfini (aéroports, gares, etc.)
     * - WITH_ADRESSE : adresse existante de l'utilisateur
     * - WITH_NEW_ADRESSE : nouvelle adresse à créer
     *
     * @var int
     */
    public int $pickupMode = ReservationService::WITH_PLACE;

    /**
     * Mode de sélection du lieu d'arrivée
     *
     * @var int
     */
    public int $dropMode = ReservationService::WITH_PLACE;

    /**
     * Mode de sélection du lieu de départ pour le retour
     *
     * @var int
     */
    public int $backPickupMode = ReservationService::WITH_PLACE;

    /**
     * Mode de sélection du lieu d'arrivée pour le retour
     *
     * @var int
     */
    public int $backDropMode = ReservationService::WITH_PLACE;

    /**
     * Indique si la réservation inclut un trajet retour
     *
     * @var bool
     */
    public bool $hasBack = false;

    // ===== DONNÉES PRINCIPALES DE LA RÉSERVATION =====

    /**
     * ID de l'entreprise pour laquelle la réservation est faite
     * En admin, déterminé par l'entreprise de la secrétaire sélectionnée
     *
     * @var int|null
     */
    public ?int $entreprise_id = null;

    /**
     * ID du passager (si mode EXIST_PASSAGER)
     *
     * @var int|null
     */
    public ?int $passager_id = null;

    /**
     * Date et heure de départ (format Y-m-d H:i)
     *
     * @var string|null
     */
    public ?string $pickup_date = null;

    /**
     * Numéro de commande ou référence client
     *
     * @var string|null
     */
    public ?string $commande = null;

    /**
     * Commentaires ou instructions spéciales
     *
     * @var string|null
     */
    public ?string $comment = null;

    /**
     * Envoyer les détails de la réservation au passager par email
     *
     * @var bool
     */
    public bool $send_to_passager = true;

    /**
     * Créer un événement de calendrier pour le passager
     *
     * @var bool
     */
    public bool $calendar_passager_invitation = true;

    /**
     * La réservation inclut des étapes intermédiaires
     *
     * @var bool
     */
    public bool $has_steps = false;

    /**
     * Description des étapes intermédiaires (JSON)
     *
     * @var string|null
     */
    public ?string $steps = null;

    // ===== LIEUX ET LOCALISATIONS =====

    /**
     * ID du lieu de départ prédéfini (si pickupMode = WITH_PLACE)
     *
     * @var int|null
     */
    public ?int $localisation_from_id = null;

    /**
     * Origine spécifique au lieu de départ (ex: terminal, porte)
     *
     * @var string|null
     */
    public ?string $pickup_origin = null;

    /**
     * ID du lieu d'arrivée prédéfini (si dropMode = WITH_PLACE)
     *
     * @var int|null
     */
    public ?int $localisation_to_id = null;

    /**
     * Origine spécifique au lieu d'arrivée
     *
     * @var string|null
     */
    public ?string $drop_off_origin = null;

    // ===== ADRESSES EXISTANTES =====

    /**
     * ID de l'adresse de départ existante (si pickupMode = WITH_ADRESSE)
     *
     * @var int|null
     */
    public ?int $addressReservationFrom = null;

    /**
     * ID de l'adresse d'arrivée existante (si dropMode = WITH_ADRESSE)
     *
     * @var int|null
     */
    public ?int $addressReservationTo = null;

    // ===== DONNÉES DYNAMIQUES POUR LA CRÉATION =====

    /**
     * Données pour créer une nouvelle adresse de départ
     * Structure : ['adresse' => '', 'code_postal' => '', 'ville' => '']
     *
     * @var array<string, mixed>
     */
    public array $newAdresseReservationFrom = [];

    /**
     * Données pour créer une nouvelle adresse d'arrivée
     *
     * @var array<string, mixed>
     */
    public array $newAdresseReservationTo = [];

    /**
     * Données pour créer un nouveau passager
     * Structure : ['nom' => '', 'email' => '', 'portable' => '', ...]
     *
     * @var array<string, mixed>
     */
    public array $newPassager = [];

    /**
     * Données pour la réservation de retour
     * Structure similaire à une réservation standard
     *
     * @var array<string, mixed>
     */
    public array $reservation_back = [];

    /**
     * Données pour créer une nouvelle adresse de départ retour
     *
     * @var array<string, mixed>
     */
    public array $newAdresseReservationFromBack = [];

    /**
     * Données pour créer une nouvelle adresse d'arrivée retour
     *
     * @var array<string, mixed>
     */
    public array $newAdresseReservationToBack = [];

    // ===== GESTION D'ERREURS COST CENTER =====

    /**
     * Indique si une erreur de cost center/type facturation est détectée
     * Se produit quand un passager existant manque ces informations
     * pour une entreprise qui les exige
     *
     * @var bool
     */
    public bool $ardianPassengerCostFacError = false;

    /**
     * Passager en erreur de cost center à corriger
     *
     * @var Passager|null
     */
    public ?Passager $passengerInError = null;

    // ===== CONTRÔLE DE VALIDATION =====

    /**
     * Active/désactive la validation du formulaire
     * Utilisé pour déléguer la validation au trait dans certains cas
     *
     * @var bool
     */
    public bool $useFormValidation = true;

    /**
     * Génère les règles de validation pour l'interface admin
     *
     * Les règles admin incluent la validation obligatoire de userId (secrétaire)
     * et gèrent la validation cost center selon l'entreprise de la secrétaire.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        // Si la validation du formulaire est désactivée, délègue au trait
        if (!$this->useFormValidation) {
            return [];
        }

        return array_merge(
            $this->getAdminBaseRules(),
            $this->getPassengerRules(),
            $this->getLocationRules(),
            $this->getBackReservationRules()
        );
    }

    public function createReservationWithRedirection(string $toRoute): void
    {
        $this->validate();
//        $this->validateReservationTiming($toRoute);
//        $this->processReservationCreation();
//        $this->saveMainReservation($toRoute);
//
//        if ($this->form->hasBack) {
//            $this->processBackReservation();
//        }
    }

    /**
     * Règles de base spécifiques à l'interface admin
     */
    private function getAdminBaseRules(): array
    {
        return [
            'userId' => 'required|string', // OBLIGATOIRE en admin
            'hasBack' => 'boolean',
            'pickup_date' => 'required|date',
            'commande' => 'nullable|string',
            'comment' => 'nullable|string',
            'send_to_passager' => 'boolean',
            'calendar_passager_invitation' => 'boolean',
            'has_steps' => 'boolean',
            'steps' => 'nullable|string',
        ];
    }

    /**
     * Règles de validation pour les passagers (admin peut créer pour n'importe qui)
     */
    private function getPassengerRules(): array
    {
        if ($this->passagerMode === ReservationService::EXIST_PASSAGER) {
            return ['passager_id' => 'required|integer'];
        }

        if ($this->passagerMode === ReservationService::NEW_PASSAGER) {
            // Utilise les règles communes du service
            $commonRules = ReservationValidationService::getCommonPassengerRules();
            $rules = [];
            foreach ($commonRules as $field => $rule) {
                $rules["newPassager.{$field}"] = $rule;
            }

            // Validation cost center pour les entreprises spécifiques (admin)
            // En admin, on utilise l'entreprise de la secrétaire pour déterminer les exigences
            if (!is_null($this->entreprise_id) && ReservationValidationService::requiresCostCenter($this->entreprise_id)) {
                $costCenterRules = ReservationValidationService::getPassengerCorrectionRules();
                foreach ($costCenterRules as $field => $rule) {
                    $rules["newPassager.{$field}"] = $rule;
                }
            }

            return $rules;
        }

        return [];
    }

    /**
     * Règles de validation pour les localisations
     */
    private function getLocationRules(): array
    {
        $rules = [];

        // Règles de départ
        $rules = array_merge($rules, $this->getPickupRules());

        // Règles d'arrivée
        $rules = array_merge($rules, $this->getDropOffRules());

        return $rules;
    }

    /**
     * Règles de validation pour le lieu de départ
     */
    private function getPickupRules(): array
    {
        return match ($this->pickupMode) {
            ReservationService::WITH_PLACE => [
                'localisation_from_id' => 'required|integer',
                'pickup_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationFrom' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => array_combine(
                array_map(fn($key) => "newAdresseReservationFrom.{$key}", array_keys(ReservationValidationService::getCommonAddressRules())),
                array_values(ReservationValidationService::getCommonAddressRules())
            ),
            default => []
        };
    }

    /**
     * Règles de validation pour le lieu d'arrivée
     */
    private function getDropOffRules(): array
    {
        return match ($this->dropMode) {
            ReservationService::WITH_PLACE => [
                'localisation_to_id' => 'required|integer',
                'drop_off_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationTo' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => array_combine(
                array_map(fn($key) => "newAdresseReservationTo.{$key}", array_keys(ReservationValidationService::getCommonAddressRules())),
                array_values(ReservationValidationService::getCommonAddressRules())
            ),
            default => []
        };
    }

    /**
     * Règles de validation pour la réservation retour
     */
    private function getBackReservationRules(): array
    {
        if (!$this->hasBack) {
            return [];
        }

        $rules = [
            'reservation_back.pickup_date' => 'required|date|after:' . ($this->pickup_date ?: 'now'), // Après la date aller
            'reservation_back.comment' => 'nullable|string',
            'reservation_back.has_steps' => 'boolean',
            'reservation_back.steps' => 'nullable|string',
        ];

        // Règles de départ retour
        $rules = array_merge($rules, match ($this->backPickupMode) {
            ReservationService::WITH_PLACE => [
                'reservation_back.localisation_from_id' => 'required|integer',
                'reservation_back.pickup_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'reservation_back.adresse_reservation_from_id' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => array_combine(
                array_map(fn($key) => "newAdresseReservationFromBack.{$key}", array_keys(ReservationValidationService::getCommonAddressRules())),
                array_values(ReservationValidationService::getCommonAddressRules())
            ),
            default => []
        });

        // Règles d'arrivée retour
        $rules = array_merge($rules, match ($this->backDropMode) {
            ReservationService::WITH_PLACE => [
                'reservation_back.localisation_to_id' => 'required|integer',
                'reservation_back.drop_off_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'reservation_back.adresse_reservation_to_id' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => array_combine(
                array_map(fn($key) => "newAdresseReservationToBack.{$key}", array_keys(ReservationValidationService::getCommonAddressRules())),
                array_values(ReservationValidationService::getCommonAddressRules())
            ),
            default => []
        });

        return $rules;
    }

    /**
     * Réinitialise les champs dépendants lors d'un changement d'utilisateur
     */
    public function resetDependentFields(): void
    {
        $this->resetMainFields();
        $this->resetAddressFields();
        $this->resetLocationFields();
        $this->resetErrorState();
        $this->resetModes();
    }

    /**
     * Réinitialise les champs principaux
     */
    private function resetMainFields(): void
    {
        $this->entreprise_id = null;
        $this->passager_id = null;
        $this->newPassager = [];
        $this->reservation_back = [];
    }

    /**
     * Réinitialise les champs d'adresses
     */
    private function resetAddressFields(): void
    {
        $this->addressReservationFrom = null;
        $this->addressReservationTo = null;
        $this->newAdresseReservationFrom = [];
        $this->newAdresseReservationTo = [];
        $this->newAdresseReservationFromBack = [];
        $this->newAdresseReservationToBack = [];
    }

    /**
     * Réinitialise les champs de localisation
     */
    private function resetLocationFields(): void
    {
        $this->localisation_from_id = null;
        $this->pickup_origin = null;
        $this->localisation_to_id = null;
        $this->drop_off_origin = null;
    }

    /**
     * Réinitialise l'état d'erreur
     */
    private function resetErrorState(): void
    {
        $this->ardianPassengerCostFacError = false;
        $this->passengerInError = null;
    }

    /**
     * Réinitialise les modes par défaut
     */
    private function resetModes(): void
    {
        $this->pickupMode = ReservationService::WITH_PLACE;
        $this->dropMode = ReservationService::WITH_PLACE;
        $this->backPickupMode = ReservationService::WITH_PLACE;
        $this->backDropMode = ReservationService::WITH_PLACE;
    }
}
