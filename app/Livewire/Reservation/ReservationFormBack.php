<?php

namespace App\Livewire\Reservation;

use App\Livewire\Forms\AdminReservationForm;
use App\Models\Reservation;
use App\Services\EventCalendar\GoogleCalendarService;
use App\Services\ReservationService;
use App\Services\ReservationValidationService;
use App\Traits\WithReservationForm;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

/**
 * Composant Livewire ReservationForm - Interface Admin
 *
 * Ce composant gère les formulaires de réservation dans l'interface d'administration.
 * Il permet aux administrateurs de créer et modifier des réservations au nom des clients.
 *
 * Fonctionnalités spécifiques admin :
 * - Sélection de secrétaire (userId) avec validation d'entreprise associée
 * - Gestion des cost centers selon l'entreprise de la secrétaire
 * - Validation contextuelle admin (entreprise de la secrétaire prioritaire)
 * - Interface complète avec tous les modes de création
 *
 * Différences avec le composant Front :
 * - Champ userId pour sélectionner la secrétaire
 * - Logique de validation basée sur l'entreprise de la secrétaire
 * - Accès à toutes les entreprises et passagers
 * - Gestion avancée des erreurs de cost center
 *
 * Routes associées :
 * - admin.reservation.create (GET/POST)
 * - admin.reservation.edit (GET/POST)
 *
 * @package App\Livewire\Reservation
 * @author MotoBleue Team
 * @version 2.0 (avec Livewire Form)
 */
class ReservationFormBack extends Component
{
    use WireUiActions, WithReservationForm;

    public AdminReservationForm $form;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation;

        // Initialize form with existing reservation data if editing
        if ($this->reservation->exists) {
            $this->form->userId = $this->reservation->passager->user->id;
            $this->form->entreprise_id = $this->reservation->entreprise_id;
            $this->form->passager_id = $this->reservation->passager_id;
            $this->form->pickup_date = $this->reservation->pickup_date?->format('Y-m-d H:i');
            $this->form->commande = $this->reservation->commande;
            $this->form->comment = $this->reservation->comment;
            $this->form->send_to_passager = $this->reservation->send_to_passager ?? true;
            $this->form->calendar_passager_invitation = $this->reservation->calendar_passager_invitation ?? true;
            $this->form->has_steps = $this->reservation->has_steps ?? false;
            $this->form->steps = $this->reservation->steps;

            // Set modes based on existing data
            if ($this->reservation->adresseReservationFrom()->exists()) {
                $this->form->pickupMode = ReservationService::WITH_ADRESSE;
                $this->form->addressReservationFrom = $this->reservation->adresse_reservation_from_id;
            } elseif ($this->reservation->localisation_from_id) {
                $this->form->pickupMode = ReservationService::WITH_PLACE;
                $this->form->localisation_from_id = $this->reservation->localisation_from_id;
                $this->form->pickup_origin = $this->reservation->pickup_origin;
            }

            if ($this->reservation->adresseReservationTo()->exists()) {
                $this->form->dropMode = ReservationService::WITH_ADRESSE;
                $this->form->addressReservationTo = $this->reservation->adresse_reservation_to_id;
            } elseif ($this->reservation->localisation_to_id) {
                $this->form->dropMode = ReservationService::WITH_PLACE;
                $this->form->localisation_to_id = $this->reservation->localisation_to_id;
                $this->form->drop_off_origin = $this->reservation->drop_off_origin;
            }
        }

        // Initialize trait properties for backward compatibility with tests
        $this->defaultReset();
    }

    public function userSelected(): void
    {
        $this->form->resetDependentFields();
    }

    public function updatedFormUserId(): void
    {
        $this->form->resetDependentFields();
    }

    public function savePassenger(): void
    {
        // Utiliser le service de validation pour les règles de correction de passager
        $this->validate(
            ReservationValidationService::getPassengerCorrectionRulesWithPrefix('form.passengerInError')
        );

        if ($this->form->passengerInError) {
            $this->form->passengerInError->updateQuietly();
            $this->form->ardianPassengerCostFacError = false;
            $this->notification()->success('Passager mis à jour', 'Les informations du passager ont été sauvegardées.');
        }
    }


    public function render(): mixed
    {
        return view('livewire.reservation.reservation-form')
            ->layout('components.layout');
    }


    /**
     * Synchronize form data to trait properties for backward compatibility
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

    public function saveReservation(GoogleCalendarService $calendarService): void
    {
        // Valider le formulaire directement
        $this->form->validate();

        // Use trait method which now uses form data
        $this->createReservationWithRedirection(route('admin.reservations.index'));

        if (\App::environment(['local', 'beta', 'prod']) && $this->reservation->exists) {
            $calendarService->createEventForSecretary($this->reservation);
        }
    }
}
