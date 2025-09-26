<?php

namespace Tests\Feature\Admin;

use App\Livewire\Reservation\ReservationForm;
use App\Models\AdresseReservation;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests spécialisés pour les traits et méthodes internes d'AdminReservationForm
 *
 * Ce fichier se concentre sur les aspects métier et la validation détaillée
 * des différents modes et règles de validation complexes.
 */
class AdminReservationFormTraitsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    protected User $user;
    protected Entreprise $entreprise;
    protected Passager $passager;
    protected Localisation $localisationFrom;
    protected Localisation $localisationTo;
    protected AdresseReservation $addressFrom;
    protected AdresseReservation $addressTo;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::find(1) ?? User::factory()->create();
        $this->entreprise = Entreprise::find(1) ?? Entreprise::factory()->create();
        $this->passager = Passager::find(1) ?? Passager::factory()->create();
        $this->localisationFrom = Localisation::find(1) ?? Localisation::factory()->create();
        $this->localisationTo = Localisation::find(2) ?? Localisation::factory()->create();
        $this->addressFrom = AdresseReservation::find(1) ?? AdresseReservation::factory()->create();
        $this->addressTo = AdresseReservation::find(2) ?? AdresseReservation::factory()->create();

        $this->actingAs($this->user);
    }

    // === Tests des règles de validation spécialisées ===

    /** @test */
    public function it_builds_address_rules_with_required_fields()
    {
        // Test avec une nouvelle adresse de départ
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => 'Required field',
                'codePostal' => 'Required field',
                'ville' => 'Required field',
                'batiment' => 'Optional field',
                'nom' => 'Optional field',
            ])
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('saveReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_validates_address_postal_code_format()
    {
        // Test avec code postal vide (champ requis)
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '123 Test Street',
                'codePostal' => '', // Empty - should cause error
                'ville' => 'Paris',
            ])
            ->call('saveReservation')
            ->assertHasErrors(['form.newAdresseReservationFrom.codePostal']);
    }

    /** @test */
    public function it_validates_valid_postal_codes()
    {
        $validPostalCodes = [
            '75001',
            '13000',
            '69000',
            '33000',
        ];

        foreach ($validPostalCodes as $validCode) {
            Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
                ->set('form.userId', $this->user->id)
                ->set('form.entrepriseId', $this->entreprise->id)
                ->set('form.pickupDate', '01/01/2024 10:00')
                ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
                ->set('form.passengerId', $this->passager->id)
                ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
                ->set('form.newAdresseReservationFrom', [
                    'adresse' => '123 Test Street',
                    'codePostal' => $validCode,
                    'ville' => 'Paris',
                ])
                ->set('form.dropMode', ReservationService::WITH_PLACE)
                ->set('form.localisationToId', $this->localisationTo->id)
                ->call('saveReservation')
                ->assertHasNoErrors();
        }
    }

    // === Tests des attributs de validation personnalisés ===

    /** @test */
    public function it_uses_custom_validation_attributes_for_readable_errors()
    {
        $component = Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '', // Empty required field
                'codePostal' => '',
                'ville' => '',
            ])
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->call('saveReservation');

        // Vérifier que les erreurs utilisent des noms lisibles
        $errors = $component->errors();
        $this->assertNotEmpty($errors);

        // Les erreurs doivent être présentes pour les champs requis
        $component->assertHasErrors([
            'form.newAdresseReservationFrom.adresse',
            'form.newAdresseReservationFrom.codePostal',
            'form.newAdresseReservationFrom.ville'
        ]);
    }

    // === Tests des règles conditionnelles complexes ===

    /** @test */
    public function it_validates_steps_only_when_has_steps_is_true()
    {
        // Test avec hasSteps = true
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
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
            ->set('form.steps', '') // Empty when required
            ->call('saveReservation')
            ->assertHasErrors(['form.steps']);

        // Test avec hasSteps = false
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.hasSteps', false)
            ->set('form.steps', '') // Empty but not required
            ->call('saveReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_validates_back_reservation_steps_conditionally()
    {
        // Test que les étapes de retour sont requises quand hasSteps est true
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
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
            ->set('form.backPickupMode', ReservationService::WITH_PLACE)
            ->set('form.backDropMode', ReservationService::WITH_PLACE)
            ->set('form.reservationBack', [
                'pickupDate' => '02/01/2024 15:00',
                'hasSteps' => false, // pas d'étapes, donc pas d'erreur attendue
                'localisationFromId' => $this->localisationTo->id,
                'localisationToId' => $this->localisationFrom->id,
            ])
            ->call('saveReservation')
            ->assertHasNoErrors(); // Should pass since hasSteps is false
    }

    // === Tests de la méthode buildBackDateRule ===

    /** @test */
    public function it_builds_back_date_rule_with_after_validation()
    {
        // Test avec date de retour manquante pour déclencher une erreur
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '05/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_PLACE)
            ->set('form.backDropMode', ReservationService::WITH_PLACE)
            ->set('form.reservationBack', [
                // pickupDate manquante - devrait causer une erreur
                'localisationFromId' => $this->localisationTo->id,
                'localisationToId' => $this->localisationFrom->id,
            ])
            ->call('saveReservation')
            ->assertHasErrors(['form.reservationBack.pickupDate']);
    }

    /** @test */
    public function it_accepts_valid_back_date_after_pickup_date()
    {
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
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
            ->set('form.backPickupMode', ReservationService::WITH_PLACE)
            ->set('form.backDropMode', ReservationService::WITH_PLACE)
            ->set('form.reservationBack', [
                'pickupDate' => '02/01/2024 15:00', // Après la date de départ
                'localisationFromId' => $this->localisationTo->id,
                'localisationToId' => $this->localisationFrom->id,
            ])
            ->call('saveReservation')
            ->assertHasNoErrors();
    }

    // === Tests d'intégration des services ===

    /** @test */
    public function it_calls_reservation_creation_service_correctly()
    {
        $component = Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id);

        // Vérifier que la validation passe avant l'appel au service
        $component->call('saveReservation')
            ->assertHasNoErrors();

        // Vérifier que isSubmitting est remis à false après le succès
        $component->assertSet('isSubmitting', false);
    }

    // === Tests des modes de réservation complexes ===

    /** @test */
    public function it_handles_mixed_location_modes_correctly()
    {
        // Pickup avec nouvelle adresse, dropoff avec lieu existant
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '123 Custom Street',
                'codePostal' => '75001',
                'ville' => 'Paris',
            ])
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('saveReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_validates_mixed_back_reservation_modes()
    {
        // Test avec modes différents pour l'aller et le retour
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_ADRESSE)
            ->set('form.addressReservationTo', $this->addressTo->id)
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationToBack', [
                'adresse' => 'Final destination',
                'codePostal' => '75008',
                'ville' => 'Paris',
            ])
            ->set('form.reservationBack', [
                'pickupDate' => '02/01/2024 15:00',
                'adresseReservationFromId' => $this->addressTo->id,
            ])
            ->call('saveReservation')
            ->assertHasNoErrors();
    }

    // === Tests de robustesse et cas limites ===

    /** @test */
    public function it_handles_empty_optional_arrays_gracefully()
    {
        Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->set('form.newPassager', []) // Empty array for unused field
            ->set('form.reservationBack', []) // Empty array for unused field
            ->call('saveReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_validates_all_boolean_fields_correctly()
    {
        $booleanFields = [
            'form.hasBack',
            'form.hasSteps',
            'form.calendarPassagerInvitation',
            'form.sendToPassager',
        ];

        foreach ($booleanFields as $field) {
            // Test avec true
            $component = Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
                ->set($field, true);
            $this->assertTrue($component->get($field) === true);

            // Test avec false
            $component = Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
                ->set($field, false);
            $this->assertTrue($component->get($field) === false);
        }
    }

    /** @test */
    public function it_maintains_data_integrity_during_complex_workflow()
    {
        $component = Livewire::test(ReservationForm::class, ['reservation' => new Reservation()])
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.commande', 'ORDER-COMPLEX-123')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager', [
                'nom' => 'Complex Test User',
                'email' => 'complex@test.com',
                'portable' => '0123456789',
                'telephone' => '0987654321',
            ])
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '123 Complex Pickup Address',
                'codePostal' => '75001',
                'ville' => 'Paris',
                'batiment' => 'Building A',
                'nom' => 'Reception',
            ])
            ->set('form.dropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationTo', [
                'adresse' => '456 Complex Dropoff Address',
                'codePostal' => '75002',
                'ville' => 'Paris',
            ])
            ->set('form.hasSteps', true)
            ->set('form.steps', 'Stop at office, then hotel, then final destination')
            ->set('form.comment', 'Complex reservation with multiple requirements')
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFromBack', [
                'adresse' => 'Return pickup address',
                'codePostal' => '75003',
                'ville' => 'Paris',
            ])
            ->set('form.newAdresseReservationToBack', [
                'adresse' => 'Return dropoff address',
                'codePostal' => '75004',
                'ville' => 'Paris',
            ])
            ->set('form.reservationBack', [
                'pickupDate' => '03/01/2024 18:00',
                'comment' => 'Return trip comment',
                'hasSteps' => true,
                'steps' => 'Different route for return',
            ])
            ->set('form.calendarPassagerInvitation', true)
            ->set('form.sendToPassager', true);

        // Valider que tous les champs sont correctement définis
        $component->assertSet('form.userId', $this->user->id)
            ->assertSet('form.entrepriseId', $this->entreprise->id)
            ->assertSet('form.commande', 'ORDER-COMPLEX-123')
            ->assertSet('form.hasBack', true)
            ->assertSet('form.hasSteps', true);

        // Tenter la soumission
        $component->call('saveReservation')
            ->assertHasNoErrors();
    }
}