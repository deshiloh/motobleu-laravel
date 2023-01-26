<?php

namespace Tests\Feature;

use App\Events\ReservationCanceled;
use App\Http\Livewire\Reservation\ReservationForm;
use App\Http\Livewire\Reservation\ReservationShow;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Mail\ReservationUpdated;
use App\Models\AdresseReservation;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Livewire\Livewire;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::find(1);
        $user->assignRole('super admin');

        $this->actingAs($user);
    }

    public function testAcessListReservationPage()
    {
        $response = $this->get(route('admin.reservations.index'));
        $response->assertStatus(200);
    }

    public function testAcessCreateReservationForm()
    {
        $response = $this->get(route('admin.reservations.create'));
        $response->assertStatus(200);
    }

    public function testAccessEditPage()
    {
        $reservation = Reservation::find(1);
        $response = $this->get(route('admin.reservations.edit', ['reservation' => $reservation->id]));
        $response->assertStatus(200);
    }

    public function testCreateBackReservatonWithDateError()
    {
        $pickupDate = Carbon::now();
        $backPickUpDate = Carbon::now()->subDay();

        Livewire::test(ReservationForm::class)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', Localisation::find(1)->id)
            ->set('dropMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_to_id', Localisation::find(2)->id)
            ->set('hasBack', true)
            ->set('reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationFromBack.adresse', 'aller de test')
            ->set('newAdresseReservationFromBack.code_postal', '34000')
            ->set('newAdresseReservationFromBack.ville', 'Montpellier')
            // BACK PLACE TO
            ->set('backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationToBack.adresse', 'arrivée de test')
            ->set('newAdresseReservationToBack.code_postal', '34000')
            ->set('newAdresseReservationToBack.ville', 'Montpellier')
            ->call('saveReservation')
            ->assertHasErrors(['reservation_back.pickup_date'])
        ;
    }

    public function testCreateReservationWithPassagerExistError()
    {
        Livewire::test(ReservationForm::class)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.passager_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithCreatePassagerEmpty()
    {
        Livewire::test(ReservationForm::class)
            ->set('passagerMode', ReservationService::NEW_PASSAGER)
            ->set('newPassager.nom', '')
            ->set('newPassager.telephone', '')
            ->set('newPassager.email', '')
            ->set('newPassager.cost_center_id', '')
            ->set('newPassager.type_facturation_id', '')
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->call('saveReservation')
            ->assertHasErrors([
                'newPassager.nom' => 'required',
                'newPassager.telephone' => 'required',
                'newPassager.email' => 'required',
                'newPassager.cost_center_id' => 'required',
                'newPassager.type_facturation_id' => 'required'
            ])
        ;
    }

    public function testCreateReservationWithPickupDateError()
    {
        Livewire::test(ReservationForm::class)
            ->set('reservation.pickup_date', null)
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.pickup_date' => 'required'
            ]);
    }

    public function testCreateReservationWithLocalisationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.localisation_from_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithAdresseReservationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('pickupMode', ReservationService::WITH_ADRESSE)
            ->set('reservation.adresse_reservation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.adresse_reservation_from_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithNewAdresseReservation()
    {
        Livewire::test(ReservationForm::class)
            ->set('pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationFrom.adresse', '')
            ->set('newAdresseReservationFrom.code_postal', '')
            ->set('newAdresseReservationFrom.ville', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'newAdresseReservationFrom.adresse' => 'required',
                'newAdresseReservationFrom.code_postal' => 'required',
                'newAdresseReservationFrom.ville' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithPlaceToExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('dropMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.localisation_to_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithAdresseReservationToExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('dropMode', ReservationService::WITH_ADRESSE)
            ->set('reservation.adresse_reservation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.adresse_reservation_to_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithNewAdresseReservationTo()
    {
        Livewire::test(ReservationForm::class)
            ->set('dropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationTo.adresse', '')
            ->set('newAdresseReservationTo.code_postal', '')
            ->set('newAdresseReservationTo.ville', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'newAdresseReservationTo.adresse' => 'required',
                'newAdresseReservationTo.code_postal' => 'required',
                'newAdresseReservationTo.ville' => 'required',
            ])
        ;
    }

    public function testCreateBackReservationWithPickupDateError()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('reservation_back.pickup_date', null)
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.pickup_date' => 'required',
            ])
        ;
    }

    public function testCreateBackReservationWithLocalisationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('backPickupMode', ReservationService::WITH_PLACE)
            ->set('reservation_back.localisation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.localisation_from_id' => 'required',
            ])
        ;
    }

    public function testCreateBackReservationWithAdresseReservationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('backPickupMode', ReservationService::WITH_ADRESSE)
            ->set('reservation_back.adresse_reservation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.adresse_reservation_from_id' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithNewAdresseReservation()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationFromBack.adresse', '')
            ->set('newAdresseReservationFromBack.code_postal', '')
            ->set('newAdresseReservationFromBack.ville', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'newAdresseReservationFromBack.adresse' => 'required',
                'newAdresseReservationFromBack.code_postal' => 'required',
                'newAdresseReservationFromBack.ville' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithPlaceToExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('backDropMode', ReservationService::WITH_PLACE)
            ->set('reservation_back.localisation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.localisation_to_id' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithAdresseReservationToExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('backDropMode', ReservationService::WITH_ADRESSE)
            ->set('reservation_back.adresse_reservation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.adresse_reservation_to_id' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithNewAdresseReservationTo()
    {
        Livewire::test(ReservationForm::class)
            ->set('hasBack', true)
            ->set('backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationToBack.adresse', '')
            ->set('newAdresseReservationToBack.code_postal', '')
            ->set('newAdresseReservationToBack.ville', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'newAdresseReservationToBack.adresse' => 'required',
                'newAdresseReservationToBack.code_postal' => 'required',
                'newAdresseReservationToBack.ville' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithPlaceAndPassagerExistOk()
    {
        $pickupDate = Carbon::now();

        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', Localisation::find(1)->id)
            ->set('reservation.localisation_to_id' , Localisation::find(2)->id)
            ->set('hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateReservationWithNewPassagerOK()
    {
        $pickupDate = Carbon::now();

        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::NEW_PASSAGER)
            ->set('newPassager.nom', 'passager test')
            ->set('newPassager.telephone', '0404')
            ->set('newPassager.email', 'passager@passager.local')
            ->set('newPassager.cost_center_id', 1)
            ->set('newPassager.type_facturation_id', 1)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', Localisation::find(1)->id)
            ->set('reservation.localisation_to_id' , Localisation::find(2)->id)
            ->set('hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(Passager::whereEmail('passager@passager.local')->exists());
        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateReservationWithExistAddressOK()
    {
        $pickupDate = Carbon::now();
        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_ADRESSE)
            ->set('dropMode', ReservationService::WITH_ADRESSE)
            ->set('reservation.adresse_reservation_from_id', AdresseReservation::find(1)->id)
            ->set('reservation.adresse_reservation_to_id' , AdresseReservation::find(2)->id)
            ->set('hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateReservationWithNewAddressOk()
    {
        $pickupDate = Carbon::now();

        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('dropMode', ReservationService::WITH_NEW_ADRESSE)
            // Adresse FROM
            ->set('newAdresseReservationFrom.adresse', 'départ de test')
            ->set('newAdresseReservationFrom.code_postal', '34000')
            ->set('newAdresseReservationFrom.ville', 'Montpellier')
            // Adresse TO
            ->set('newAdresseReservationTo.adresse', 'Arrivée de test')
            ->set('newAdresseReservationTo.code_postal', '34000')
            ->set('newAdresseReservationTo.ville', 'Montpellier')
            ->set('hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(AdresseReservation::whereAdresse('départ de test')->exists());
        $this->assertTrue(AdresseReservation::whereAdresse('Arrivée de test')->exists());
        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateBackReservationWithPlaceOk()
    {
        $pickupDate = Carbon::now();
        $backPickUpDate = Carbon::now()->addDay();

        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', Localisation::find(1)->id)
            ->set('dropMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_to_id', Localisation::find(2)->id)
            ->set('hasBack', true)
            ->set('reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('backPickupMode', ReservationService::WITH_PLACE)
            ->set('reservation_back.localisation_from_id', Localisation::find(1)->id)
            // BACK PLACE TO
            ->set('backDropMode', ReservationService::WITH_PLACE)
            ->set('reservation_back.localisation_to_id', Localisation::find(1)->id)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
        $this->assertTrue(Reservation::wherePickupDate($backPickUpDate)->exists());
    }

    public function testCreateBackReservationWithExistedAddressOk(): void
    {
        $pickupDate = Carbon::now();
        $backPickUpDate = Carbon::now()->addDay();

        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', Localisation::find(1)->id)
            ->set('dropMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_to_id', Localisation::find(2)->id)
            ->set('hasBack', true)
            ->set('reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('backPickupMode', ReservationService::WITH_ADRESSE)
            ->set('reservation_back.adresse_reservation_from_id', AdresseReservation::find(1)->id)
            // BACK PLACE TO
            ->set('backDropMode', ReservationService::WITH_ADRESSE)
            ->set('reservation_back.adresse_reservation_to_id', AdresseReservation::find(1)->id)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
        $this->assertTrue(Reservation::wherePickupDate($backPickUpDate)->exists());
    }

    public function testCreateBackReservationWithNewAddressOk()
    {
        $pickupDate = Carbon::now();
        $backPickUpDate = Carbon::now()->addDay();

        Livewire::test(ReservationForm::class)
            ->set('userId', 1)
            ->set('reservation.entreprise_id', 1)
            ->set('passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('reservation.passager_id', Passager::find(1)->id)
            ->set('reservation.pickup_date', $pickupDate)
            ->set('pickupMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_from_id', Localisation::find(1)->id)
            ->set('dropMode', ReservationService::WITH_PLACE)
            ->set('reservation.localisation_to_id', Localisation::find(2)->id)
            ->set('hasBack', true)
            ->set('reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationFromBack.adresse', 'aller de test')
            ->set('newAdresseReservationFromBack.code_postal', '34000')
            ->set('newAdresseReservationFromBack.ville', 'Montpellier')
            // BACK PLACE TO
            ->set('backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('newAdresseReservationToBack.adresse', 'arrivée de test')
            ->set('newAdresseReservationToBack.code_postal', '34000')
            ->set('newAdresseReservationToBack.ville', 'Montpellier')
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.reservations.index'))
        ;

        $this->assertTrue(AdresseReservation::whereAdresse('aller de test')->exists());
        $this->assertTrue(AdresseReservation::whereAdresse('arrivée de test')->exists());
        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
        $this->assertTrue(Reservation::wherePickupDate($backPickUpDate)->exists());
    }

    public function the_component_can_render(): void
    {
        $reservation = Reservation::find(1);

        $component = Livewire::test(ReservationShow::class, ['reservation' => $reservation]);

        $component->assertStatus(200);
    }

    public function testConfirmedReservation(): void
    {
        $reservation = Reservation::find(1);
        Livewire::test(ReservationShow::class, ['reservation' => $reservation])
            ->set('reservation.pilote_id', null)
            ->call('confirmedAction')
            ->assertHasErrors([
                'reservation.pilote_id' => 'required'
            ])
        ;
    }

    public function testReservationConfirmOk(): void
    {
        \Mail::fake();

        $reservation = Reservation::find(1);
        $pilote = Pilote::find(1);
        Livewire::test(ReservationShow::class, ['reservation' => $reservation])
            ->set('reservation.pilote_id', $pilote->id)
            ->call('confirmedAction')
            ->assertHasNoErrors()
        ;
        $this->assertTrue(Reservation::where('is_confirmed', true)->exists());

        \Mail::assertSent(PiloteAttached::class);
    }

    public function testReservationCancelOk(): void
    {
        \Event::fake();

        $reservation = Reservation::find(1);

        Livewire::test(ReservationShow::class, ['reservation' => $reservation])
            ->call('cancelAction')
            ->assertHasNoErrors()
        ;
        $this->assertTrue(Reservation::where([
            ['is_confirmed', '=', false],
            ['is_cancel', '=', true]
        ])->exists());

        \Event::assertDispatched(ReservationCanceled::class);
    }

    public function testUpdatePiloteOk()
    {
        \Mail::fake();

        $reservation = Reservation::find(1);
        $newPilote = Pilote::factory()->create();

        Livewire::test(ReservationShow::class, ['reservation' => $reservation])
            ->set('reservation.pilote_id', $newPilote->id)
            ->call('updatePilote')
            ->assertHasNoErrors();

        \Mail::assertSent(PiloteDetached::class);
        \Mail::assertSent(PiloteAttached::class);
    }

    public function testReservationEditWihPiloteSuccess()
    {
        \Mail::fake();

        $reservation = Reservation::find(1);
        Livewire::test(ReservationForm::class, ['reservation' => $reservation])
            ->set('reservation.commande', 'test')
            ->call('saveReservation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reservations', [
            'commande' => 'test'
        ]);

        \Mail::assertSent(ReservationUpdated::class);
    }
}
