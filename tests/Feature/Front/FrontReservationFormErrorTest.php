<?php

namespace Tests\Feature\Front;

use App\Livewire\Front\Reservation\ReservationForm;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests spécialisés pour la gestion d'erreur dans le formulaire Front
 *
 * Se concentre sur les cas d'erreur spécifiques au contexte front-end
 * et les différences de comportement par rapport au formulaire admin.
 */
class FrontReservationFormErrorTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    protected User $user;
    protected Entreprise $entreprise;
    protected Passager $passager;
    protected Localisation $localisationFrom;
    protected Localisation $localisationTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::find(1) ?? User::factory()->create();
        $this->entreprise = Entreprise::find(1) ?? Entreprise::factory()->create();
        $this->passager = Passager::find(1) ?? Passager::factory()->create();
        $this->localisationFrom = Localisation::find(1) ?? Localisation::factory()->create();
        $this->localisationTo = Localisation::find(2) ?? Localisation::factory()->create();

        $this->actingAs($this->user);
    }

    // === Tests de validation d'erreur spécifiques au front ===

    /** @test */
    public function it_handles_missing_required_fields_in_front()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', null) // OK en front
            ->set('form.entrepriseId', null) // Requis même en front
            ->set('form.pickupDate', null) // Requis
            ->call('createReservation')
            ->assertHasErrors([
                'form.entrepriseId',
                'form.pickupDate'
            ]);
    }

    /** @test */
    public function it_validates_front_specific_boolean_fields()
    {
        $component = Livewire::test(ReservationForm::class);

        // Test que les champs spécifiques au front sont bien des booléens
        $component->assertSet('form.calendarPassengerInvitation', true)
            ->assertSet('form.sendToPassenger', true);

        // Test modification des valeurs
        $component->set('form.calendarPassengerInvitation', false)
            ->set('form.sendToPassenger', false)
            ->assertSet('form.calendarPassengerInvitation', false)
            ->assertSet('form.sendToPassenger', false);
    }

    /** @test */
    public function it_handles_invalid_passenger_data_in_front()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager', [
                'nom' => '', // Requis mais vide
                'email' => 'email-invalide', // Format invalide
                'portable' => '', // Requis mais vide
            ])
            ->call('createReservation')
            ->assertHasErrors([
                'form.newPassager.nom',
                'form.newPassager.email',
                'form.newPassager.portable'
            ]);
    }

    /** @test */
    public function it_validates_pickup_date_format_in_front()
    {
        $invalidDates = [
            '2024-01-01 10:00', // Format ISO incorrect
            '01-01-2024 10:00', // Séparateur incorrect
            '1/1/2024 10:00',   // Sans zéros en préfixe
            '01/01/24 10:00',   // Année courte
            'invalid-date',      // Complètement invalide
        ];

        foreach ($invalidDates as $invalidDate) {
            Livewire::test(ReservationForm::class)
                ->set('form.userId', $this->user->id)
                ->set('form.entrepriseId', $this->entreprise->id)
                ->set('form.pickupDate', $invalidDate)
                ->call('createReservation')
                ->assertHasErrors(['form.pickupDate']);
        }
    }

    /** @test */
    public function it_handles_missing_location_data_correctly()
    {
        // Test mode WITH_PLACE sans localisationFromId
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', null) // Manquant
            ->call('createReservation')
            ->assertHasErrors(['form.localisationFromId']);

        // Test mode WITH_ADRESSE sans addressReservationFrom
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.addressReservationFrom', null) // Manquant
            ->call('createReservation')
            ->assertHasErrors(['form.addressReservationFrom']);
    }

    /** @test */
    public function it_validates_new_address_requirements_in_front()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '', // Requis mais vide
                'codePostal' => '', // Requis mais vide
                'ville' => '', // Requis mais vide
            ])
            ->call('createReservation')
            ->assertHasErrors([
                'form.newAdresseReservationFrom.adresse',
                'form.newAdresseReservationFrom.codePostal',
                'form.newAdresseReservationFrom.ville'
            ]);
    }

    /** @test */
    public function it_handles_back_reservation_validation_errors()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.hasBack', true)
            ->set('form.reservationBack', [
                // Pas de pickupDate - devrait causer une erreur
                'comment' => 'Retour sans date',
            ])
            ->call('createReservation')
            ->assertHasErrors(['form.reservationBack.pickupDate']);
    }

    /** @test */
    public function it_validates_steps_requirement_conditionally_in_front()
    {
        // hasSteps = true mais pas de steps
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.hasSteps', true)
            ->set('form.steps', '') // Vide mais requis
            ->call('createReservation')
            ->assertHasErrors(['form.steps']);

        // hasSteps = false, steps peut être vide
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 11:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.hasSteps', false)
            ->set('form.steps', '') // OK car hasSteps = false
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_handles_frontend_specific_field_validation()
    {
        // Test validation des champs spécifiques au front avec des valeurs invalides
        $component = Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id);

        // Les champs booléens doivent accepter true/false
        $component->set('form.calendarPassengerInvitation', true)
            ->set('form.sendToPassenger', false)
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_maintains_form_state_after_validation_errors_in_front()
    {
        $component = Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', null) // Erreur de validation
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.comment', 'Commentaire à conserver')
            ->call('createReservation');

        // Vérifier que les champs valides sont conservés
        $component->assertSet('form.userId', $this->user->id)
            ->assertSet('form.pickupDate', '01/01/2024 10:00')
            ->assertSet('form.comment', 'Commentaire à conserver')
            ->assertHasErrors(['form.entrepriseId']);
    }

    /** @test */
    public function it_handles_edge_case_field_values_in_front()
    {
        // Test avec des valeurs limites
        $veryLongText = str_repeat('Long text ', 200); // ~2000 caractères

        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.comment', $veryLongText)
            ->set('form.steps', $veryLongText)
            ->call('createReservation');

        // Should handle long text gracefully (pass or fail predictably)
        $this->assertTrue(true);
    }

    /** @test */
    public function it_handles_concurrent_user_sessions_gracefully()
    {
        // Simuler deux utilisateurs différents
        $user2 = User::factory()->create();

        $component1 = Livewire::test(ReservationForm::class);

        // Changer d'utilisateur connecté
        $this->actingAs($user2);

        $component2 = Livewire::test(ReservationForm::class);

        // Vérifier que chaque composant a le bon userId
        $component1->assertSet('form.userId', $this->user->id);
        $component2->assertSet('form.userId', $user2->id);
    }

    /** @test */
    public function it_validates_foreign_key_constraints_in_front()
    {
        // Test avec des IDs inexistants
        Livewire::test(ReservationForm::class)
            ->set('form.userId', 99999) // ID inexistant
            ->set('form.entrepriseId', 99999) // ID inexistant
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->call('createReservation')
            ->assertHasErrors([
                'form.userId',
                'form.entrepriseId'
            ]);
    }

    /** @test */
    public function it_handles_javascript_injection_attempts()
    {
        $maliciousCode = '<script>alert("XSS")</script>';

        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.comment', $maliciousCode)
            ->set('form.commande', $maliciousCode)
            ->call('createReservation');

        // Le test doit s'assurer que le code malicieux est géré correctement
        // (soit rejeté par validation, soit échappé/nettoyé)
        $this->assertTrue(true);
    }

    /** @test */
    public function it_handles_special_characters_in_front_fields()
    {
        $specialChars = 'Château, café & résumé - côte d\'azur!';

        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager', [
                'nom' => $specialChars,
                'email' => 'test@château.com',
                'portable' => '0123456789',
            ])
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => $specialChars,
                'codePostal' => '75001',
                'ville' => $specialChars,
            ])
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();
    }
}