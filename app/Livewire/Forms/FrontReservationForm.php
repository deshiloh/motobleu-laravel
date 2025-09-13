<?php

namespace App\Livewire\Forms;

use App\Models\Passager;
use App\Services\ReservationService;
use Livewire\Form;

/**
 * Classe FrontReservationForm - Livewire Form pour l'interface Client
 *
 * Cette classe représente le formulaire Livewire pour la création et modification
 * de réservations dans l'interface client. Elle est simplifiée par rapport à
 * l'interface admin et adaptée à l'usage client.
 *
 * Fonctionnalités spécifiques front :
 * - Pas de sélection de secrétaire (utilisateur connecté implicite)
 * - Validation cost center basée sur l'entreprise sélectionnée directement
 * - Accès limité aux entreprises de l'utilisateur connecté
 * - Interface optimisée pour l'expérience client
 * - Restrictions de sécurité renforcées
 *
 * @package App\Livewire\Forms
 * @author MotoBleue Team
 * @version 2.0
 */
class FrontReservationForm extends Form
{
    // ===== MODES DE SÉLECTION =====

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
     * ID de l'entreprise sélectionnée par l'utilisateur
     * Limité aux entreprises auxquelles l'utilisateur connecté appartient
     *
     * @var int|null
     */
    public ?int $entreprise_id = null;

    /**
     * ID du passager (si mode EXIST_PASSAGER)
     * Limité aux passagers de l'utilisateur connecté
     *
     * @var int|null
     */
    public ?int $passager_id = null;

    /**
     * Date et heure de départ (format Y-m-d H:i)
     * Validation front : minimum 15 minutes dans le futur
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
     * Par défaut activé en interface client
     *
     * @var bool
     */
    public bool $send_to_passager = true;

    /**
     * Créer un événement de calendrier pour le passager
     * Par défaut activé en interface client
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
     * Limité aux adresses de l'utilisateur connecté
     *
     * @var int|null
     */
    public ?int $addressReservationFrom = null;

    /**
     * ID de l'adresse d'arrivée existante (si dropMode = WITH_ADRESSE)
     * Limité aux adresses de l'utilisateur connecté
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
     * En front, automatiquement lié à l'utilisateur connecté
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

    // ===== CHAMP SPÉCIFIQUE FRONT (RÉTROCOMPATIBILITÉ) =====

    /**
     * ID de l'utilisateur (pour rétrocompatibilité avec le trait)
     * En front, correspond à l'utilisateur connecté (auth()->id())
     * N'est PAS éditable par l'utilisateur front
     *
     * @var string|null
     */
    public ?string $userId = '';

    /**
     * Génère les règles de validation pour l'interface client
     *
     * Les règles front sont plus strictes sur les dates et n'incluent pas
     * la validation userId (utilisateur connecté implicite).
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
            $this->getFrontBaseRules(),
            $this->getPassengerRules(),
            $this->getLocationRules(),
            $this->getBackReservationRules()
        );
    }

    /**
     * Règles de base spécifiques à l'interface client
     */
    private function getFrontBaseRules(): array
    {
        return [
            'hasBack' => 'boolean',
            'entreprise_id' => 'required|integer', // Obligatoire en front
            'pickup_date' => 'required|date|after:' . now()->addMinutes(14)->toDateTimeString(), // Min 15 min dans le futur
            'commande' => 'nullable|string',
            'comment' => 'nullable|string',
            'send_to_passager' => 'boolean',
            'calendar_passager_invitation' => 'boolean',
            'has_steps' => 'boolean',
            'steps' => 'nullable|string',
        ];
    }

    /**
     * Règles de validation pour les passagers (front limité aux passagers de l'utilisateur)
     */
    private function getPassengerRules(): array
    {
        if ($this->passagerMode === ReservationService::EXIST_PASSAGER) {
            return ['passager_id' => 'required|integer'];
        }

        if ($this->passagerMode === ReservationService::NEW_PASSAGER) {
            $rules = [
                'newPassager.nom' => 'required|string',
                'newPassager.email' => 'required|email',
                'newPassager.portable' => 'required|string',
                'newPassager.telephone' => 'nullable|string',
            ];

            // Validation cost center pour les entreprises spécifiques
            if (!is_null($this->entreprise_id) &&
                in_array($this->entreprise_id, \app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation)) {
                $rules['newPassager.cost_center_id'] = 'required';
                $rules['newPassager.type_facturation_id'] = 'required';
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
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationFrom.adresse' => 'required|string',
                'newAdresseReservationFrom.code_postal' => 'required|string',
                'newAdresseReservationFrom.ville' => 'required|string'
            ],
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
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationTo.adresse' => 'required|string',
                'newAdresseReservationTo.code_postal' => 'required|string',
                'newAdresseReservationTo.ville' => 'required|string'
            ],
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
            'reservation_back.pickup_date' => 'required|date|after:pickup_date', // Après la date aller
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
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationFromBack.adresse' => 'required|string',
                'newAdresseReservationFromBack.code_postal' => 'required|string',
                'newAdresseReservationFromBack.ville' => 'required|string'
            ],
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
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationToBack.adresse' => 'required|string',
                'newAdresseReservationToBack.code_postal' => 'required|string',
                'newAdresseReservationToBack.ville' => 'required|string'
            ],
            default => []
        });

        return $rules;
    }

    /**
     * Réinitialise les champs dépendants (version simplifiée front)
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