<?php

namespace App\Traits;

use App\Livewire\Forms\AdminReservationForm;
use App\Livewire\Forms\FrontReservationForm;
use App\Mail\ReservationUpdated;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Services\ReservationService;
use app\Settings\BillSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

/**
 * Trait WithReservationForm
 *
 * Ce trait fournit la logique complète pour gérer les formulaires de réservation
 * dans les composants Livewire. Il intègre le Livewire Form avec la logique métier
 * et gère les validations contextuelles (admin vs front).
 *
 * Fonctionnalités principales :
 * - Validation dynamique selon le mode (nouveau passager, passager existant)
 * - Gestion des adresses (existantes, nouvelles, lieux prédéfinis)
 * - Support des réservations aller-retour
 * - Validation cost center pour les entreprises spécifiques
 * - Synchronisation between Livewire Form et propriétés du trait
 *
 * Contextes supportés :
 * - Admin : utilise l'entreprise de la secrétaire sélectionnée
 * - Front : utilise l'entreprise sélectionnée par l'utilisateur
 *
 * @package App\Traits
 * @author MotoBleue Team
 * @version 2.0 (avec Livewire Form)
 */
trait WithReservationForm
{
    // La propriété $form est déclarée dans chaque composant avec son type spécifique
    // AdminReservationForm pour l'admin, FrontReservationForm pour le front

    // ===== MODÈLES CORE =====

    /**
     * Réservation principale (aller)
     *
     * @var Reservation
     */
    public Reservation $reservation;

    /**
     * Réservation de retour (optionnelle)
     *
     * @var Reservation
     */
    public Reservation $reservation_back;

    /**
     * Nouveau passager à créer (si passagerMode = NEW_PASSAGER)
     *
     * @var Passager
     */
    public Passager $newPassager;

    /**
     * Nouvelle adresse de départ à créer (si pickupMode = WITH_NEW_ADRESSE)
     *
     * @var AdresseReservation
     */
    public AdresseReservation $newAdresseReservationFrom;

    /**
     * Nouvelle adresse d'arrivée à créer (si dropMode = WITH_NEW_ADRESSE)
     *
     * @var AdresseReservation
     */
    public AdresseReservation $newAdresseReservationTo;

    /**
     * Nouvelle adresse de départ retour (si backPickupMode = WITH_NEW_ADRESSE)
     *
     * @var AdresseReservation
     */
    public AdresseReservation $newAdresseReservationFromBack;

    /**
     * Nouvelle adresse d'arrivée retour (si backDropMode = WITH_NEW_ADRESSE)
     *
     * @var AdresseReservation
     */
    public AdresseReservation $newAdresseReservationToBack;

    // ===== PROPRIÉTÉS DE RÉTROCOMPATIBILITÉ =====

    /**
     * Mode de sélection du passager
     * - EXIST_PASSAGER : passager existant
     * - NEW_PASSAGER : nouveau passager à créer
     *
     * @var int
     */
    public int $passagerMode = ReservationService::EXIST_PASSAGER;

    /**
     * Mode de sélection du lieu de départ
     * - WITH_PLACE : lieu prédéfini
     * - WITH_ADRESSE : adresse existante
     * - WITH_NEW_ADRESSE : nouvelle adresse
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
     * Mode de sélection du lieu de départ retour
     *
     * @var int
     */
    public int $backPickupMode = ReservationService::WITH_PLACE;

    /**
     * Mode de sélection du lieu d'arrivée retour
     *
     * @var int
     */
    public int $backDropMode = ReservationService::WITH_PLACE;

    /**
     * Indique si la réservation a un trajet retour
     *
     * @var bool
     */
    public bool $hasBack = false;

    /**
     * ID de l'utilisateur sélectionné (contexte admin)
     * En mode admin, ceci représente la secrétaire sélectionnée
     *
     * @var string|null
     */
    public ?string $userId = '';

    /**
     * Erreur de validation cost center pour les entreprises Ardian
     *
     * @var bool
     */
    public bool $ardianPassengerCostFacError = false;

    /**
     * Passager en erreur de cost center/type facturation
     *
     * @var Passager|null
     */
    public ?Passager $passengerInError = null;

    /**
     * ID de l'adresse de réservation de départ sélectionnée
     *
     * @var int|null
     */
    public ?int $addressReservationFrom = null;

    /**
     * ID de l'adresse de réservation d'arrivée sélectionnée
     *
     * @var int|null
     */
    public ?int $addressReservationTo = null;

    // ===== CACHE DE VALIDATION =====

    /**
     * Cache des règles de validation générées
     *
     * @var array<string, string>
     */
    public array $generatedRules = [];

    // ===== PROTECTION CONTRE LES MISES À JOUR RÉCURSIVES =====

    /**
     * Flag pour éviter les mises à jour récursives lors du changement d'utilisateur
     *
     * @var bool
     */
    private static bool $userIdUpdateInProgress = false;

    // ===== MÉTHODES DE VALIDATION =====

    /**
     * Génère les règles de validation dynamiques selon le contexte
     *
     * Cette méthode est le point d'entrée principal pour la validation.
     * Elle synchronise d'abord les données du form vers le trait,
     * puis génère les règles appropriées selon le contexte.
     *
     * @return array<string, string> Règles de validation Laravel
     */
    protected function rules(): array
    {
        // Synchronise les données du form vers les propriétés du trait
        $this->syncFormToTraitProperties();

        // Gestion spéciale des erreurs cost center/type facturation
        if ($this->ardianPassengerCostFacError) {
            return $this->getPassengerErrorRules();
        }

        // Génération des règles standard
        return $this->buildValidationRules();
    }

    /**
     * Règles de validation pour les erreurs de cost center
     *
     * Utilisées quand un passager existant n'a pas de cost center
     * ou type facturation pour une entreprise qui l'exige.
     *
     * @return array<string, string>
     */
    private function getPassengerErrorRules(): array
    {
        return [
            'reservation.send_to_passager' => 'bool',
            'reservation.calendar_passager_invitation' => 'bool',
            'reservation.entreprise_id' => 'required',
            'passengerInError.cost_center_id' => 'required',
            'passengerInError.type_facturation_id' => 'required'
        ];
    }

    /**
     * Construit les règles de validation dynamiques
     *
     * Cette méthode détermine le contexte (admin vs front) et génère
     * les règles de validation appropriées via le ReservationService.
     *
     * Logique contextuelle :
     * - En contexte admin : utilise l'entreprise de la secrétaire (userId)
     * - En contexte front : utilise l'entreprise sélectionnée
     *
     * @return array<string, string> Règles de validation générées
     */
    private function buildValidationRules(): array
    {
        $this->generatedRules = [];

        // Détermine l'entreprise à utiliser selon le contexte
        $companyId = $this->form->entreprise_id;
        if (!empty($this->form->userId) && $this->isAdminContext()) {
            // Contexte admin : utilise l'entreprise de la secrétaire
            $user = \App\Models\User::find($this->form->userId);
            if ($user && $user->entreprises()->count() > 0) {
                $companyId = $user->entreprises()->first()->id;
            }
        }

        // Génération des règles via le service
        ReservationService::generateDefaultRules($this->generatedRules);
        ReservationService::generatePassagerFromRules($this->generatedRules, $this->form->passagerMode, $companyId);
        ReservationService::generateFromLocalisationRules($this->generatedRules, $this->form->pickupMode, $this->reservation);
        ReservationService::generateToLocalisationRules($this->generatedRules, $this->form->dropMode, $this->reservation);

        // Règles pour le trajet retour si nécessaire
        if ($this->form->hasBack) {
            ReservationService::generateFromLocalisationBackRules($this->generatedRules, $this->form->backPickupMode);
            ReservationService::generateToLocalisationBackRules($this->generatedRules, $this->form->backDropMode);
        }

        return $this->generatedRules;
    }

    private function defaultReset(): void
    {
        $this->initializeModels();
        $this->setReservationDefaults();
        $this->syncTraitPropertiesToForm();
    }

    private function initializeModels(): void
    {
        $this->reservation_back = new Reservation();
        $this->newPassager = new Passager();
        $this->newAdresseReservationFrom = new AdresseReservation();
        $this->newAdresseReservationTo = new AdresseReservation();
        $this->newAdresseReservationFromBack = new AdresseReservation();
        $this->newAdresseReservationToBack = new AdresseReservation();
    }

    private function setReservationDefaults(): void
    {
        $this->reservation->send_to_passager = true;
        $this->reservation->calendar_passager_invitation = true;
        $this->reservation->has_steps = $this->reservation->steps !== null;

        $this->reservation_back->send_to_passager = true;
        $this->reservation_back->calendar_passager_invitation = true;
        $this->reservation_back->has_steps = false;
    }

    public function updatedUserId(): void
    {
        if (self::$userIdUpdateInProgress) {
            return;
        }

        self::$userIdUpdateInProgress = true;

        try {
            $this->form->userId = $this->userId;
            $this->form->resetDependentFields();
            $this->resetDependentFieldsOnUserChange();
        } finally {
            self::$userIdUpdateInProgress = false;
        }
    }

    private function resetDependentFieldsOnUserChange(): void
    {
        $this->reservation->entreprise_id = null;
        $this->reservation->passager_id = null;
        $this->form->entreprise_id = null;
        $this->form->passager_id = null;
        $this->addressReservationFrom = null;
        $this->addressReservationTo = null;
        $this->form->addressReservationFrom = null;
        $this->form->addressReservationTo = null;
        $this->ardianPassengerCostFacError = false;
        $this->passengerInError = null;
        $this->form->ardianPassengerCostFacError = false;
        $this->form->passengerInError = null;
    }

    public function updatedReservationPassagerId(): void
    {
        if ($this->reservation->passager_id) {
            $this->form->passager_id = $this->reservation->passager_id;
            $this->validatePassengerCostCenter();
        }
    }

    public function updatedReservationEntrepriseId(): void
    {
        if ($this->reservation->entreprise_id) {
            $this->form->entreprise_id = $this->reservation->entreprise_id;
            $this->validatePassengerCostCenter();
        }
    }

    private function validatePassengerCostCenter(): void
    {
        if (!$this->shouldValidateCostCenter()) {
            $this->clearCostCenterError();
            return;
        }

        $passenger = $this->reservation->passager;
        if (!$passenger) {
            return;
        }

        $this->passengerInError = $passenger;
        $this->form->passengerInError = $passenger;
        $this->ardianPassengerCostFacError = $this->hasIncompleteCostCenterInfo($passenger);
        $this->form->ardianPassengerCostFacError = $this->ardianPassengerCostFacError;
    }

    private function shouldValidateCostCenter(): bool
    {
        $passagerId = $this->form->passager_id ?? $this->reservation->passager_id;

        // In admin context, use the company of the selected user (secretary)
        $entrepriseId = $this->form->entreprise_id ?? $this->reservation->entreprise_id;
        if (!empty($this->form->userId) && $this->isAdminContext()) {
            $user = \App\Models\User::find($this->form->userId);
            if ($user && $user->entreprises()->count() > 0) {
                $entrepriseId = $user->entreprises()->first()->id;
            }
        }

        if (!$passagerId || !$entrepriseId) {
            return false;
        }

        $billSettings = \app(BillSettings::class);
        return in_array($entrepriseId, $billSettings->entreprises_cost_center_facturation);
    }

    private function hasIncompleteCostCenterInfo(Passager $passenger): bool
    {
        return !$passenger->costCenter()->exists() && !$passenger->typeFacturation()->exists();
    }

    private function clearCostCenterError(): void
    {
        $this->ardianPassengerCostFacError = false;
        $this->passengerInError = null;
        $this->form->ardianPassengerCostFacError = false;
        $this->form->passengerInError = null;
    }

    public function savePassenger(): void
    {
        $this->validate();

        if ($this->passengerInError) {
            $this->passengerInError->updateQuietly();
            $this->reservation->passager_id = $this->passengerInError->id;
            $this->form->passager_id = $this->passengerInError->id;
        }

        $this->ardianPassengerCostFacError = false;
        $this->form->ardianPassengerCostFacError = false;
    }

    private function createReservationWithRedirection(string $toRoute): void
    {
        $this->validateReservationTiming($toRoute);
        $this->processReservationCreation();
        $this->saveMainReservation($toRoute);

        if ($this->form->hasBack) {
            $this->processBackReservation();
        }
    }

    private function validateReservationTiming(string $toRoute): void
    {

        $this->withValidator(function (Validator $validator) use ($toRoute) {
            $validator->after(function ($validator) use ($toRoute) {
                $this->validatePickupDate($validator, $toRoute);
                $this->validateBackReservationDate($validator);
            });
        })->validate();
    }

    private function validatePickupDate(Validator $validator, string $toRoute): void
    {
        if (str_contains($toRoute, 'admin') || !$this->reservation->pickup_date) {
            return;
        }

        $validReservationDate = Carbon::now()->addMinutes(14);
        if (!$this->reservation->pickup_date->greaterThanOrEqualTo($validReservationDate)) {
            $validator->errors()->add('reservation.pickup_date', 'Les réservations effectuées moins de 15 minutes avant l\'heure actuelle ne sont pas autorisées.');
        }
    }

    private function validateBackReservationDate(Validator $validator): void
    {
        if (!$this->form->hasBack || empty($this->form->pickup_date)) {
            return;
        }

        $pickupDate = $this->form->pickup_date;
        $backPickupDate = isset($this->form->reservation_back['pickup_date']) ? $this->form->reservation_back['pickup_date'] : null;

        if ($pickupDate && $backPickupDate) {
            $pickupDate = \Carbon\Carbon::parse($pickupDate);
            $backPickupDate = \Carbon\Carbon::parse($backPickupDate);

            if ($pickupDate->greaterThanOrEqualTo($backPickupDate)) {
                $validator->errors()->add('reservation_back.pickup_date', 'Date incorrect');
            }
        }
    }

    private function processReservationCreation(): void
    {
        // Sync form data to models before processing
        $this->syncFormToTraitProperties();

        $this->handlePassengerCreation();
        $this->processPickupLocation();
        $this->processDropOffLocation();
        $this->cleanupSteps();
    }

    private function handlePassengerCreation(): void
    {
        if ($this->form->passagerMode !== ReservationService::NEW_PASSAGER) {
            return;
        }

        // Dissocie le passager existant si nécessaire
        if ($this->reservation->passager()->exists()) {
            $this->reservation->passager()->disassociate();
        }

        // Crée le nouveau passager
        $this->fillModelFromArray($this->newPassager, $this->form->newPassager);
        $this->newPassager->user_id = $this->form->userId;
        $this->newPassager->save();

        // Associe le nouveau passager à la réservation
        $this->reservation->passager_id = $this->newPassager->id;
        $this->form->passager_id = $this->newPassager->id;
    }

    private function processPickupLocation(): void
    {
        $this->processLocation(
            type: 'pickup',
            mode: $this->form->pickupMode,
            addressId: $this->form->addressReservationFrom ?? $this->addressReservationFrom,
            formData: $this->form->newAdresseReservationFrom,
            addressModel: $this->newAdresseReservationFrom
        );
    }

    private function processDropOffLocation(): void
    {
        $this->processLocation(
            type: 'dropoff',
            mode: $this->form->dropMode,
            addressId: $this->form->addressReservationTo ?? $this->addressReservationTo,
            formData: $this->form->newAdresseReservationTo,
            addressModel: $this->newAdresseReservationTo
        );
    }

    /**
     * Traite la gestion d'une localisation (pickup ou dropoff) selon le mode
     *
     * @param string $type Type de localisation ('pickup' ou 'dropoff')
     * @param int $mode Mode de sélection (WITH_PLACE, WITH_ADRESSE, WITH_NEW_ADRESSE)
     * @param int|null $addressId ID de l'adresse existante
     * @param array $formData Données du formulaire pour nouvelle adresse
     * @param AdresseReservation $addressModel Modèle d'adresse à utiliser
     */
    private function processLocation(string $type, int $mode, ?int $addressId, array $formData, AdresseReservation $addressModel): void
    {
        match ($mode) {
            ReservationService::WITH_PLACE => $this->handleLocationPlace($type),
            ReservationService::WITH_ADRESSE => $this->handleLocationAddress($type, $addressId),
            ReservationService::WITH_NEW_ADRESSE => $this->handleLocationNewAddress($type, $formData, $addressModel),
            default => null
        };
    }

    /**
     * Gère le mode lieu prédéfini - dissocie l'adresse existante
     */
    private function handleLocationPlace(string $type): void
    {
        $addressRelation = $type === 'pickup' ? 'adresseReservationFrom' : 'adresseReservationTo';

        if ($this->reservation->{$addressRelation}()->exists()) {
            $this->reservation->{$addressRelation}()->disassociate();
        }
    }

    /**
     * Gère le mode adresse existante - associe l'adresse sélectionnée
     */
    private function handleLocationAddress(string $type, ?int $addressId): void
    {
        $localisationRelation = $type === 'pickup' ? 'localisationFrom' : 'localisationTo';
        $addressRelation = $type === 'pickup' ? 'adresseReservationFrom' : 'adresseReservationTo';
        $originField = $type === 'pickup' ? 'pickup_origin' : 'drop_off_origin';

        // Dissocie la localisation existante
        if ($this->reservation->{$localisationRelation}()->exists()) {
            $this->reservation->{$localisationRelation}()->disassociate();
        }

        // Associe l'adresse si fournie
        if ($addressId) {
            if ($this->reservation->{$addressRelation}()->exists()) {
                $this->reservation->{$addressRelation}()->disassociate();
            }

            $address = AdresseReservation::find($addressId);
            $this->reservation->{$addressRelation}()->associate($address);
        }

        // Nettoie l'origine
        $this->reservation->{$originField} = null;
    }

    /**
     * Gère le mode nouvelle adresse - crée et associe une nouvelle adresse
     */
    private function handleLocationNewAddress(string $type, array $formData, AdresseReservation $addressModel): void
    {
        $localisationRelation = $type === 'pickup' ? 'localisationFrom' : 'localisationTo';
        $addressRelation = $type === 'pickup' ? 'adresseReservationFrom' : 'adresseReservationTo';
        $originField = $type === 'pickup' ? 'pickup_origin' : 'drop_off_origin';

        // Dissocie la localisation existante
        if ($this->reservation->{$localisationRelation}()->exists()) {
            $this->reservation->{$localisationRelation}()->disassociate();
        }

        // Remplit le modèle avec les données du formulaire
        $this->fillModelFromArray($addressModel, $formData);

        // Sauvegarde et associe la nouvelle adresse
        $addressModel->user_id = $this->reservation->passager->user->id;
        $addressModel->save();
        $this->reservation->{$addressRelation}()->associate($addressModel);

        // Nettoie l'origine
        $this->reservation->{$originField} = null;
    }

    private function cleanupSteps(): void
    {
        if (!$this->reservation->has_steps && $this->reservation->steps !== null) {
            $this->reservation->steps = null;
        }
    }

    private function saveMainReservation(string $toRoute): void
    {
        try {
            $contacts = $this->getNotificationContacts();
            $this->reservation->save();
            $this->sendReservationNotifications($contacts);
            $this->showSuccessAndRedirect($toRoute);
        } catch (\Exception $exception) {
            $this->handleReservationError($exception);
        }
    }

    private function getNotificationContacts(): array
    {
        if (!$this->reservation->exists || !$this->reservation->isDirty() || !$this->reservation->passager) {
            return [];
        }

        $contacts = [$this->reservation->passager->user->email];

        if ($this->reservation->send_to_passager) {
            $contacts[] = $this->reservation->passager->email;
        }

        return array_filter($contacts);
    }

    private function sendReservationNotifications(array $contacts): void
    {
        foreach ($contacts as $contact) {
            \Mail::to($contact)->send(new ReservationUpdated($this->reservation));
        }
    }

    private function showSuccessAndRedirect(string $toRoute): void
    {
        session()->flash('success', 'Traitement de la réservation traité avec succès.');
        redirect()->to($toRoute);
    }

    private function handleReservationError(\Exception $exception): void
    {
        $this->handleError(
            exception: $exception,
            title: 'Création impossible',
            message: 'Une erreur est survenue pendant la création de la réservation',
            context: ['reservation' => $this->reservation]
        );
    }

    private function processBackReservation(): void
    {
        $this->reservation->has_back = true;
        $this->setupBackReservationData();
        $this->processBackReservationAddresses();
        $this->saveBackReservation();
    }

    private function setupBackReservationData(): void
    {
        $this->reservation_back->passager_id = $this->reservation->passager_id;
        $this->reservation_back->entreprise_id = $this->reservation->entreprise_id;
        $this->reservation_back->commande = $this->reservation->commande;
        $this->reservation_back->send_to_passager = $this->reservation->send_to_passager;
        $this->reservation_back->calendar_passager_invitation = $this->reservation->calendar_passager_invitation;

        if (!$this->reservation_back->has_steps && $this->reservation_back->steps !== null) {
            $this->reservation_back->steps = null;
        }
    }

    private function processBackReservationAddresses(): void
    {
        // Traite l'adresse de départ retour
        if ($this->form->backPickupMode === ReservationService::WITH_NEW_ADRESSE) {
            $this->createAndAssociateBackAddress(
                formData: $this->form->newAdresseReservationFromBack,
                addressModel: $this->newAdresseReservationFromBack,
                relation: 'adresseReservationFrom'
            );
        }

        // Traite l'adresse d'arrivée retour
        if ($this->form->backDropMode === ReservationService::WITH_NEW_ADRESSE) {
            $this->createAndAssociateBackAddress(
                formData: $this->form->newAdresseReservationToBack,
                addressModel: $this->newAdresseReservationToBack,
                relation: 'adresseReservationTo'
            );
        }
    }

    /**
     * Crée et associe une nouvelle adresse pour la réservation retour
     */
    private function createAndAssociateBackAddress(array $formData, AdresseReservation $addressModel, string $relation): void
    {
        // Remplit le modèle avec les données du formulaire
        $this->fillModelFromArray($addressModel, $formData);

        // Sauvegarde et associe l'adresse
        $addressModel->user_id = $this->reservation->passager->user->id;
        $addressModel->save();
        $this->reservation_back->{$relation}()->associate($addressModel);
    }

    private function saveBackReservation(): void
    {
        try {
            $this->reservation_back->save();
            $this->reservation->reservationBack()->associate($this->reservation_back->id);
            $this->reservation->updateQuietly(['reservation_id' => $this->reservation_back->id]);
        } catch (\Exception $exception) {
            $this->handleBackReservationError($exception);
        }
    }

    private function handleBackReservationError(\Exception $exception): void
    {
        $this->handleError(
            exception: $exception,
            title: 'Création impossible',
            message: 'Une erreur est survenue pendant la création de la réservation de retour',
            context: [
                'reservation' => $this->reservation,
                'reservation_back' => $this->reservation_back
            ]
        );
    }

    /**
     * Synchronize form data to trait properties for compatibility
     */
    private function syncFormToTraitProperties(): void
    {
        // Form -> Trait sync
        if ($this->form->userId) $this->userId = $this->form->userId;
        if ($this->form->passagerMode) $this->passagerMode = $this->form->passagerMode;
        if ($this->form->pickupMode) $this->pickupMode = $this->form->pickupMode;
        if ($this->form->dropMode) $this->dropMode = $this->form->dropMode;
        $this->hasBack = $this->form->hasBack;
        if ($this->form->backPickupMode) $this->backPickupMode = $this->form->backPickupMode;
        if ($this->form->backDropMode) $this->backDropMode = $this->form->backDropMode;
        if ($this->form->addressReservationFrom) $this->addressReservationFrom = $this->form->addressReservationFrom;
        if ($this->form->addressReservationTo) $this->addressReservationTo = $this->form->addressReservationTo;
        $this->ardianPassengerCostFacError = $this->form->ardianPassengerCostFacError;
        $this->passengerInError = $this->form->passengerInError;

        // Sync reservation data from form
        if ($this->form->entreprise_id) $this->reservation->entreprise_id = $this->form->entreprise_id;
        if ($this->form->passager_id) $this->reservation->passager_id = $this->form->passager_id;
        if ($this->form->pickup_date) $this->reservation->pickup_date = $this->form->pickup_date;
        if ($this->form->commande) $this->reservation->commande = $this->form->commande;
        if ($this->form->comment) $this->reservation->comment = $this->form->comment;
        $this->reservation->send_to_passager = $this->form->send_to_passager;
        $this->reservation->calendar_passager_invitation = $this->form->calendar_passager_invitation;
        $this->reservation->has_steps = $this->form->has_steps;
        if ($this->form->steps) $this->reservation->steps = $this->form->steps;
        if ($this->form->localisation_from_id) $this->reservation->localisation_from_id = $this->form->localisation_from_id;
        if ($this->form->pickup_origin) $this->reservation->pickup_origin = $this->form->pickup_origin;
        if ($this->form->localisation_to_id) $this->reservation->localisation_to_id = $this->form->localisation_to_id;
        if ($this->form->drop_off_origin) $this->reservation->drop_off_origin = $this->form->drop_off_origin;

        // Sync complex data arrays to models
        if (!empty($this->form->newPassager)) {
            foreach ($this->form->newPassager as $key => $value) {
                $this->newPassager->{$key} = $value;
            }
        }

        if (!empty($this->form->newAdresseReservationFrom)) {
            foreach ($this->form->newAdresseReservationFrom as $key => $value) {
                $this->newAdresseReservationFrom->{$key} = $value;
            }
        }

        if (!empty($this->form->newAdresseReservationTo)) {
            foreach ($this->form->newAdresseReservationTo as $key => $value) {
                $this->newAdresseReservationTo->{$key} = $value;
            }
        }

        if (!empty($this->form->newAdresseReservationFromBack)) {
            foreach ($this->form->newAdresseReservationFromBack as $key => $value) {
                $this->newAdresseReservationFromBack->{$key} = $value;
            }
        }

        if (!empty($this->form->newAdresseReservationToBack)) {
            foreach ($this->form->newAdresseReservationToBack as $key => $value) {
                $this->newAdresseReservationToBack->{$key} = $value;
            }
        }

        if (!empty($this->form->reservation_back)) {
            foreach ($this->form->reservation_back as $key => $value) {
                $this->reservation_back->{$key} = $value;
            }
        }
    }

    /**
     * Synchronize trait properties to form (for tests and backward compatibility)
     */
    private function syncTraitPropertiesToForm(): void
    {
        // Trait -> Form sync
        if ($this->userId && !$this->form->userId) $this->form->userId = $this->userId;
        if ($this->passagerMode && !$this->form->passagerMode) $this->form->passagerMode = $this->passagerMode;
        if ($this->pickupMode && !$this->form->pickupMode) $this->form->pickupMode = $this->pickupMode;
        if ($this->dropMode && !$this->form->dropMode) $this->form->dropMode = $this->dropMode;
        $this->form->hasBack = $this->hasBack;
        if ($this->backPickupMode && !$this->form->backPickupMode) $this->form->backPickupMode = $this->backPickupMode;
        if ($this->backDropMode && !$this->form->backDropMode) $this->form->backDropMode = $this->backDropMode;
        if ($this->addressReservationFrom && !$this->form->addressReservationFrom) $this->form->addressReservationFrom = $this->addressReservationFrom;
        if ($this->addressReservationTo && !$this->form->addressReservationTo) $this->form->addressReservationTo = $this->addressReservationTo;
        $this->form->ardianPassengerCostFacError = $this->ardianPassengerCostFacError;
        $this->form->passengerInError = $this->passengerInError;

        // Sync reservation data to form
        if ($this->reservation->entreprise_id && !$this->form->entreprise_id) $this->form->entreprise_id = $this->reservation->entreprise_id;
        if ($this->reservation->passager_id && !$this->form->passager_id) $this->form->passager_id = $this->reservation->passager_id;
        if ($this->reservation->pickup_date && !$this->form->pickup_date) $this->form->pickup_date = $this->reservation->pickup_date;
        if ($this->reservation->commande && !$this->form->commande) $this->form->commande = $this->reservation->commande;
        if ($this->reservation->comment && !$this->form->comment) $this->form->comment = $this->reservation->comment;
        if (!isset($this->form->send_to_passager)) $this->form->send_to_passager = $this->reservation->send_to_passager ?? true;
        if (!isset($this->form->calendar_passager_invitation)) $this->form->calendar_passager_invitation = $this->reservation->calendar_passager_invitation ?? true;
        if (!isset($this->form->has_steps)) $this->form->has_steps = $this->reservation->has_steps ?? false;
        if ($this->reservation->steps && !$this->form->steps) $this->form->steps = $this->reservation->steps;
        if ($this->reservation->localisation_from_id && !$this->form->localisation_from_id) $this->form->localisation_from_id = $this->reservation->localisation_from_id;
        if ($this->reservation->pickup_origin && !$this->form->pickup_origin) $this->form->pickup_origin = $this->reservation->pickup_origin;
        if ($this->reservation->localisation_to_id && !$this->form->localisation_to_id) $this->form->localisation_to_id = $this->reservation->localisation_to_id;
        if ($this->reservation->drop_off_origin && !$this->form->drop_off_origin) $this->form->drop_off_origin = $this->reservation->drop_off_origin;
    }

    /**
     * Check if we are in admin context based on the current component
     */
    private function isAdminContext(): bool
    {
        // Check if the current component is from Admin namespace
        return str_contains(get_class($this), '\\Livewire\\Reservation\\');
    }

    // ===== MÉTHODES UTILITAIRES POUR ÉLIMINER LA DUPLICATION =====

    /**
     * Remplit un modèle Eloquent avec les données d'un tableau
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $data
     */
    private function fillModelFromArray($model, array $data): void
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
    }

    /**
     * Gère les erreurs de manière unifiée avec notification et logging
     *
     * @param \Exception $exception
     * @param string $title
     * @param string $message
     * @param array $context
     */
    private function handleError(\Exception $exception, string $title, string $message, array $context = []): void
    {
        // Notification à l'utilisateur
        $this->notification()->error($title, $message);

        // Debug local avec Ray
        if (App::environment(['local'])) {
            ray($context)->exception($exception);
        }

        // Logging Sentry pour tous les environnements
        Log::channel('sentry')->error('Erreur pendant la création / édition d\'une réservation', array_merge([
            'exception' => $exception,
        ], $context));
    }
}
