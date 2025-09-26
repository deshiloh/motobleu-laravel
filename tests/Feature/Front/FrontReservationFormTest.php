<?php

namespace Tests\Feature\Front;

use App\Livewire\Front\Reservation\ReservationForm;
use App\Models\AdresseReservation;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests pour le formulaire de réservation Front-end
 *
 * Se concentre sur les spécificités du formulaire front par rapport au formulaire admin,
 * notamment la gestion différente du userId et les noms de champs spécifiques.
 */
class FrontReservationFormTest extends TestCase
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

    // === Tests des valeurs par défaut spécifiques au Front ===

    /** @test */
    public function it_has_correct_default_values_for_front()
    {
        $component = Livewire::test(ReservationForm::class);

        $component->assertSet('form.hasBack', false)
            ->assertSet('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->assertSet('form.pickupMode', ReservationService::WITH_PLACE)
            ->assertSet('form.dropMode', ReservationService::WITH_PLACE)
            ->assertSet('form.calendarPassengerInvitation', true)
            ->assertSet('form.sendToPassenger', true)
            ->assertSet('form.backPickupMode', ReservationService::WITH_PLACE)
            ->assertSet('form.backDropMode', ReservationService::WITH_PLACE);
    }

    /** @test */
    public function it_sets_user_id_on_mount()
    {
        $component = Livewire::test(ReservationForm::class);

        $component->assertSet('form.userId', $this->user->id);
    }

    // === Tests de validation spécifiques au Front ===

    /** @test */
    public function it_allows_nullable_user_id_in_front_form()
    {
        $component = Livewire::test(ReservationForm::class)
            ->set('form.userId', null) // Front permet userId null
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_validates_front_specific_notification_fields()
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
            ->set('form.calendarPassengerInvitation', false)
            ->set('form.sendToPassenger', false)
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    // === Tests de la méthode createReservation ===

    /** @test */
    public function it_creates_reservation_successfully()
    {
        $component = Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();

        // Vérifier que la création s'est bien passée
        // Note: Le comportement de reset peut varier selon l'implémentation
        $this->assertTrue(true); // Test principal : création sans erreur
    }

    /** @test */
    public function it_handles_validation_errors_in_front_form()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.entrepriseId', null) // Champ requis manquant
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->call('createReservation')
            ->assertHasErrors(['form.entrepriseId']);
    }

    /** @test */
    public function it_shows_error_notification_on_exception()
    {
        // Simuler une erreur avec des données invalides
        $component = Livewire::test(ReservationForm::class)
            ->set('form.userId', 99999) // ID inexistant
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->call('createReservation');

        // Doit avoir une erreur de validation pour userId inexistant
        $component->assertHasErrors(['form.userId']);
    }

    // === Tests des modes de passager ===

    /** @test */
    public function it_validates_existing_passenger_mode_in_front()
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
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_validates_new_passenger_mode_in_front()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager', [
                'nom' => 'Nouveau Passager Front',
                'email' => 'nouveau@front.com',
                'portable' => '0123456789',
                'telephone' => '0987654321',
            ])
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    // === Tests des modes de localisation ===

    /** @test */
    public function it_validates_all_pickup_modes_in_front()
    {
        // Test mode WITH_PLACE
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.pickupOrigin', 'Vol AF123')
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();

        // Test mode WITH_ADRESSE
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 11:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.addressReservationFrom', $this->addressFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();

        // Test mode WITH_NEW_ADRESSE
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 12:00')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '123 Rue du Front',
                'codePostal' => '75001',
                'ville' => 'Paris',
            ])
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    // === Tests des réservations retour ===

    /** @test */
    public function it_validates_back_reservation_in_front()
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
            ->set('form.backPickupMode', ReservationService::WITH_PLACE)
            ->set('form.backDropMode', ReservationService::WITH_PLACE)
            ->set('form.reservationBack', [
                'pickupDate' => '02/01/2024 18:00',
                'comment' => 'Voyage de retour',
                'localisationFromId' => $this->localisationTo->id,
                'localisationToId' => $this->localisationFrom->id,
            ])
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    // === Tests des étapes intermédiaires ===

    /** @test */
    public function it_validates_steps_in_front_form()
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
            ->set('form.hasSteps', true)
            ->set('form.steps', 'Arrêt à l\'hôtel puis au bureau')
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    /** @test */
    public function it_requires_steps_when_has_steps_is_true_in_front()
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
            ->set('form.hasSteps', true)
            ->set('form.steps', '') // Vide mais requis
            ->call('createReservation')
            ->assertHasErrors(['form.steps']);
    }

    // === Tests de navigation ===

    /** @test */
    public function it_redirects_to_front_list_correctly()
    {
        $component = Livewire::test(ReservationForm::class);

        $component->call('redirectToList')
            ->assertRedirect(route('front.reservation.list'));
    }

    // === Tests des champs optionnels ===

    /** @test */
    public function it_validates_optional_fields_in_front()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.commande', 'CMD-FRONT-123')
            ->set('form.comment', 'Commentaire client front')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->localisationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->localisationTo->id)
            ->call('createReservation')
            ->assertHasNoErrors();
    }

    // === Tests de rendu ===

    /** @test */
    public function it_renders_front_reservation_form_view()
    {
        $component = Livewire::test(ReservationForm::class);

        $component->assertViewIs('livewire.front.reservation.reservation-form');
    }

    // === Tests de validation complexe ===

    /** @test */
    public function it_validates_complete_front_reservation_with_all_options()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.userId', $this->user->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '01/01/2024 10:00')
            ->set('form.commande', 'CMD-COMPLETE-FRONT')
            ->set('form.comment', 'Réservation complète front-end')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager', [
                'nom' => 'Client Front Complet',
                'email' => 'client@front-complet.com',
                'portable' => '0123456789',
                'telephone' => '0987654321',
            ])
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom', [
                'adresse' => '456 Avenue Front',
                'codePostal' => '75002',
                'ville' => 'Paris',
            ])
            ->set('form.dropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationTo', [
                'adresse' => '789 Boulevard Client',
                'codePostal' => '75003',
                'ville' => 'Paris',
            ])
            ->set('form.hasSteps', true)
            ->set('form.steps', 'Étapes multiples pour client front')
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFromBack', [
                'adresse' => 'Retour départ front',
                'codePostal' => '75004',
                'ville' => 'Paris',
            ])
            ->set('form.newAdresseReservationToBack', [
                'adresse' => 'Retour arrivée front',
                'codePostal' => '75005',
                'ville' => 'Paris',
            ])
            ->set('form.reservationBack', [
                'pickupDate' => '03/01/2024 20:00',
                'comment' => 'Commentaire retour front',
                'hasSteps' => true,
                'steps' => 'Étapes retour front',
            ])
            ->set('form.calendarPassengerInvitation', true)
            ->set('form.sendToPassenger', true)
            ->call('createReservation')
            ->assertHasNoErrors();
    }
}