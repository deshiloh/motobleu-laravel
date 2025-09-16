<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Forms\AdminReservationForm;
use App\Livewire\Reservation\ReservationForm;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use App\Settings\BillSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

    class ReservationFormTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $secretary;
    private Entreprise $entreprise;
    private Passager $passager;
    private Localisation $locationFrom;
    private Localisation $locationTo;
    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake events to avoid Google Calendar issues in tests
        Event::fake();

        $this->user = User::factory()->create(['is_admin' => true]);
        $this->secretary = User::factory()->create(['is_admin' => false]);
        $this->entreprise = Entreprise::factory()->create();

        // Associate secretary with entreprise
        $this->secretary->entreprises()->attach($this->entreprise);

        $this->passager = Passager::factory()->create([
            'user_id' => $this->secretary->id
        ]);

        $this->locationFrom = Localisation::factory()->create();
        $this->locationTo = Localisation::factory()->create();

        $this->reservation = new Reservation();

        $this->actingAs($this->user);
    }

    public function test_component_can_render(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->assertStatus(200)
            ->assertViewIs('livewire.reservation.reservation-form');
    }

    public function test_mount_sets_reservation_property(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->assertSet('reservation', $this->reservation);
    }

    public function test_form_property_is_initialized(): void
    {
        $component = Livewire::test(ReservationForm::class, ['reservation' => $this->reservation]);

        $this->assertInstanceOf(AdminReservationForm::class, $component->get('form'));
        $component->assertSet('form.passengerMode', ReservationService::EXIST_PASSAGER)
                  ->assertSet('form.pickupMode', ReservationService::WITH_PLACE);
    }

    public function test_changing_user_id_resets_passenger_id(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.passengerId', 123)
            ->set('form.userId', $this->secretary->id)
            ->assertSet('form.passengerId', 123) // Should remain set
            ->set('form.userId', null)
            ->assertSet('form.passengerId', null); // Should be reset
    }

    public function test_form_validation_fails_with_empty_required_fields(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->call('saveReservation')
            ->assertHasErrors([
                'form.userId',
                'form.entrepriseId',
                'form.pickupDate'
            ]);
    }

    public function test_form_validation_fails_with_invalid_pickup_date_format(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '2024-01-01 12:00') // Wrong format
            ->call('saveReservation')
            ->assertHasErrors('form.pickupDate');
    }

    public function test_form_validation_passes_with_valid_pickup_date_format(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30') // Correct format
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->assertHasNoErrors('form.pickupDate');
    }

    public function test_existing_passenger_mode_validation(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->call('saveReservation')
            ->assertHasErrors('form.passengerId');
    }

    public function test_new_passenger_mode_validation(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->call('saveReservation')
            ->assertHasErrors([
                'form.newPassager.nom',
                'form.newPassager.email',
                'form.newPassager.portable'
            ]);
    }

    public function test_pickup_place_mode_validation(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->call('saveReservation')
            ->assertHasErrors('form.localisationFromId');
    }

    public function test_pickup_address_mode_validation(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_ADRESSE)
            ->call('saveReservation')
            ->assertHasErrors('form.addressReservationFrom');
    }

    public function test_pickup_new_address_mode_validation(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->call('saveReservation')
            ->assertHasErrors([
                'form.newAdresseReservationFrom.adresse',
                'form.newAdresseReservationFrom.codePostal',
                'form.newAdresseReservationFrom.ville'
            ]);
    }

    public function test_drop_place_mode_validation(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->call('saveReservation')
            ->assertHasErrors('form.localisationToId');
    }

    public function test_steps_validation_when_enabled(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->set('form.hasSteps', true)
            ->call('saveReservation')
            ->assertHasErrors('form.steps');
    }

    public function test_successful_reservation_creation_with_existing_passenger_and_places(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reservations', [
            'entreprise_id' => $this->entreprise->id,
            'passager_id' => $this->passager->id,
            'localisation_from_id' => $this->locationFrom->id,
            'localisation_to_id' => $this->locationTo->id,
        ]);
    }

    public function test_successful_reservation_creation_with_new_passenger(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager.nom', 'John Doe')
            ->set('form.newPassager.email', 'john@example.com')
            ->set('form.newPassager.portable', '0123456789')
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('passagers', [
            'nom' => 'John Doe',
            'email' => 'john@example.com',
            'portable' => '0123456789',
            'user_id' => $this->secretary->id,
        ]);
    }

    public function test_successful_reservation_creation_with_new_addresses(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom.adresse', '123 Rue de la Paix')
            ->set('form.newAdresseReservationFrom.codePostal', '75001')
            ->set('form.newAdresseReservationFrom.ville', 'Paris')
            ->set('form.dropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationTo.adresse', '456 Avenue des Champs')
            ->set('form.newAdresseReservationTo.codePostal', '75008')
            ->set('form.newAdresseReservationTo.ville', 'Paris')
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('adresse_reservations', [
            'adresse' => '123 Rue de la Paix',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'user_id' => $this->secretary->id,
        ]);

        $this->assertDatabaseHas('adresse_reservations', [
            'adresse' => '456 Avenue des Champs',
            'code_postal' => '75008',
            'ville' => 'Paris',
            'user_id' => $this->secretary->id,
        ]);
    }

    public function test_reservation_creation_with_steps(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->set('form.hasSteps', true)
            ->set('form.steps', 'Arrêt intermédiaire au 123 Rue Example')
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reservations', [
            'has_steps' => true,
            'steps' => 'Arrêt intermédiaire au 123 Rue Example',
        ]);
    }

    public function test_reservation_creation_with_comment(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->set('form.comment', 'Commentaire de test')
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reservations', [
            'comment' => 'Commentaire de test',
        ]);
    }

    public function test_reservation_creation_with_pickup_origin(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->set('form.pickupDate', '15/01/2024 14:30')
            ->set('form.passengerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisationFromId', $this->locationFrom->id)
            ->set('form.pickupOrigin', 'Terminal 2E')
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisationToId', $this->locationTo->id)
            ->set('form.dropOffOrigin', 'Quai 3')
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reservations', [
            'pickup_origin' => 'Terminal 2E',
            'drop_off_origin' => 'Quai 3',
        ]);
    }

    public function test_component_handles_null_values(): void
    {
        // Test that the component handles null values properly
        $component = Livewire::test(ReservationForm::class, ['reservation' => $this->reservation]);

        $component->set('form.userId', null)
            ->set('form.entrepriseId', null)
            ->call('saveReservation')
            ->assertHasErrors(['form.userId', 'form.entrepriseId']);
    }

    public function test_form_resets_passenger_when_user_changes(): void
    {
        Livewire::test(ReservationForm::class, ['reservation' => $this->reservation])
            ->set('form.userId', $this->secretary->id)
            ->set('form.passengerId', $this->passager->id)
            ->set('form.userId', null)
            ->assertSet('form.passengerId', null);
    }

    public function test_updated_method_handles_other_properties(): void
    {
        $component = Livewire::test(ReservationForm::class, ['reservation' => $this->reservation]);

        // Test that other properties don't affect passenger ID
        $component->set('form.passengerId', $this->passager->id)
            ->set('form.entrepriseId', $this->entreprise->id)
            ->assertSet('form.passengerId', $this->passager->id);
    }
}
