<?php

namespace Tests\Feature\Front;

use App\Livewire\Front\Reservation\ReservationForm;
use App\Models\AdresseReservation;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use app\Settings\BillSettings;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Livewire\Livewire;
use Tests\TestCase;

class FrontFormReservationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;
    private Carbon $pickupDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::find(1));
        $this->pickupDate = Carbon::now()->addMinutes(15);

        \Event::fake();
    }

    public function testCreateBackReservatonWithDateError()
    {
        $pickupDate = Carbon::now();
        $backPickUpDate = Carbon::now()->subDay();

        Livewire::test(ReservationForm::class)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', Localisation::find(1)->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_to_id', Localisation::find(2)->id)
            ->set('form.hasBack', true)
            ->set('form.reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('form.backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFromBack.adresse', 'aller de test')
            ->set('form.newAdresseReservationFromBack.code_postal', '34000')
            ->set('form.newAdresseReservationFromBack.ville', 'Montpellier')
            // BACK PLACE TO
            ->set('form.backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationToBack.adresse', 'arrivée de test')
            ->set('form.newAdresseReservationToBack.code_postal', '34000')
            ->set('form.newAdresseReservationToBack.ville', 'Montpellier')
            ->call('saveReservation')
            ->assertHasErrors(['reservation_back.pickup_date'])
        ;
    }

    public function testCreateReservationWithPassagerExistError()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.passager_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithCreatePassagerEmpty()
    {
        BillSettings::fake([
            'entreprises_cost_center_facturation' => [1]
        ]);

        Livewire::test(ReservationForm::class)
            ->set('form.passagerMode', ReservationService::NEW_PASSAGER)
            ->set('form.entreprise_id', 1)
            ->set('form.newPassager', [
                'nom' => null,
                'telephone' => '',
                'email' => null,
                'cost_center_id' => null,
                'type_facturation_id' => null
            ])
            ->set('form.userId', 1)
            ->call('saveReservation')
            ->assertHasErrors([
                'newPassager.nom' => 'required',
                'newPassager.email' => 'required',
                'newPassager.cost_center_id' => 'required',
                'newPassager.type_facturation_id' => 'required'
            ])
        ;
    }

    public function testCreateReservationWithPickupDateError()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.pickup_date', null)
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.pickup_date' => 'required'
            ]);
    }

    public function testCreateReservationWithLocalisationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.localisation_from_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithAdresseReservationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.pickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.addressReservationFrom')
            ->call('saveReservation')
            ->assertHasErrors([
                'addressReservationFrom' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithNewAdresseReservation()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFrom.adresse', '')
            ->set('form.newAdresseReservationFrom.code_postal', '')
            ->set('form.newAdresseReservationFrom.ville', '')
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
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation.localisation_to_id' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithAdresseReservationToExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.dropMode', ReservationService::WITH_ADRESSE)
            ->set('form.addressReservationTo')
            ->call('saveReservation')
            ->assertHasErrors([
                'addressReservationTo' => 'required',
            ])
        ;
    }

    public function testCreateReservationWithNewAdresseReservationTo()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.dropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationTo.adresse', '')
            ->set('form.newAdresseReservationTo.code_postal', '')
            ->set('form.newAdresseReservationTo.ville', '')
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
            ->set('form.hasBack', true)
            ->set('form.reservation_back.pickup_date', null)
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.pickup_date' => 'required',
            ])
        ;
    }

    public function testCreateBackReservationWithLocalisationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_PLACE)
            ->set('form.reservation_back.localisation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.localisation_from_id' => 'required',
            ])
        ;
    }

    public function testCreateBackReservationWithAdresseReservationExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.reservation_back.adresse_reservation_from_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.adresse_reservation_from_id' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithNewAdresseReservation()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.hasBack', true)
            ->set('form.backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFromBack.adresse', '')
            ->set('form.newAdresseReservationFromBack.code_postal', '')
            ->set('form.newAdresseReservationFromBack.ville', '')
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
            ->set('form.hasBack', true)
            ->set('form.backDropMode', ReservationService::WITH_PLACE)
            ->set('form.reservation_back.localisation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.localisation_to_id' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithAdresseReservationToExist()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.hasBack', true)
            ->set('form.backDropMode', ReservationService::WITH_ADRESSE)
            ->set('form.reservation_back.adresse_reservation_to_id', '')
            ->call('saveReservation')
            ->assertHasErrors([
                'reservation_back.adresse_reservation_to_id' => 'required',
            ])
        ;
    }

    public function testBackCreateReservationWithNewAdresseReservationTo()
    {
        Livewire::test(ReservationForm::class)
            ->set('form.hasBack', true)
            ->set('form.backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationToBack.adresse', '')
            ->set('form.newAdresseReservationToBack.code_postal', '')
            ->set('form.newAdresseReservationToBack.ville', '')
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
        $pickupDate = Carbon::now()->addMinutes(15);

        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 1)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', Localisation::find(1)->id)
            ->set('form.localisation_to_id' , Localisation::find(2)->id)
            ->set('form.hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateReservationWithNewPassagerOK()
    {
        $pickupDate = Carbon::now()->addMinutes(15);
        BillSettings::fake([
            'entreprises_cost_center_facturation' => [1]
        ]);

        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 20)
            ->set('form.passagerMode', ReservationService::NEW_PASSAGER)
            ->set('form.newPassager.nom', 'passager test')
            ->set('form.newPassager.telephone')
            ->set('form.newPassager.portable', '087655')
            ->set('form.newPassager.email', 'passager@passager.local')
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', Localisation::find(1)->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_to_id', Localisation::find(2)->id)
            ->set('form.hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))
        ;

        $this->assertTrue(Passager::whereEmail('passager@passager.local')->exists());
        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateReservationWithExistAddressOK()
    {
        $pickupDate = Carbon::now()->addMinutes(15);
        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 1)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.dropMode', ReservationService::WITH_ADRESSE)
            ->set('form.addressReservationFrom', AdresseReservation::find(1)->id)
            ->set('form.addressReservationTo', AdresseReservation::find(2)->id)
            ->set('form.hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateReservationWithNewAddressOk()
    {
        $pickupDate = Carbon::now()->addMinutes(15);

        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 1)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.dropMode', ReservationService::WITH_NEW_ADRESSE)
            // Adresse FROM
            ->set('form.newAdresseReservationFrom.adresse', 'départ de test')
            ->set('form.newAdresseReservationFrom.code_postal', '34000')
            ->set('form.newAdresseReservationFrom.ville', 'Montpellier')
            // Adresse TO
            ->set('form.newAdresseReservationTo.adresse', 'Arrivée de test')
            ->set('form.newAdresseReservationTo.code_postal', '34000')
            ->set('form.newAdresseReservationTo.ville', 'Montpellier')
            ->set('form.hasBack', false)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))
        ;

        $this->assertTrue(AdresseReservation::whereAdresse('départ de test')->exists());
        $this->assertTrue(AdresseReservation::whereAdresse('Arrivée de test')->exists());
        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
    }

    public function testCreateBackReservationWithPlaceOk()
    {
        $pickupDate = Carbon::now()->addMinutes(15);
        $backPickUpDate = Carbon::now()->addDay();

        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 1)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', Localisation::find(1)->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_to_id', Localisation::find(2)->id)
            ->set('form.hasBack', true)
            ->set('form.reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('form.backPickupMode', ReservationService::WITH_PLACE)
            ->set('form.reservation_back.localisation_from_id', Localisation::find(1)->id)
            // BACK PLACE TO
            ->set('form.backDropMode', ReservationService::WITH_PLACE)
            ->set('form.reservation_back.localisation_to_id', Localisation::find(1)->id)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
        $this->assertTrue(Reservation::wherePickupDate($backPickUpDate)->exists());
    }

    public function testCreateBackReservationWithExistedAddressOk(): void
    {
        $pickupDate = Carbon::now()->addMinutes(15);
        $backPickUpDate = Carbon::now()->addDay();

        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 1)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.has_steps', true)
            ->set('form.steps', "Je suis un test")
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', Localisation::find(1)->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_to_id', Localisation::find(2)->id)
            ->set('form.hasBack', true)
            ->set('form.reservation_back.pickup_date', $backPickUpDate)
            // BACK PLACE FROM
            ->set('form.backPickupMode', ReservationService::WITH_ADRESSE)
            ->set('form.reservation_back.adresse_reservation_from_id', AdresseReservation::find(1)->id)
            // BACK PLACE TO
            ->set('form.backDropMode', ReservationService::WITH_ADRESSE)
            ->set('form.reservation_back.adresse_reservation_to_id', AdresseReservation::find(1)->id)
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))
        ;

        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
        $this->assertTrue(Reservation::wherePickupDate($backPickUpDate)->exists());
    }

    public function testCreateBackReservationWithNewAddressOk()
    {
        $pickupDate = Carbon::now()->addMinutes(15);
        $backPickUpDate = Carbon::now()->addDay();

        Livewire::test(ReservationForm::class)
            ->set('form.userId', 1)
            ->set('form.entreprise_id', 1)
            ->set('form.passagerMode', ReservationService::EXIST_PASSAGER)
            ->set('form.passager_id', Passager::find(1)->id)
            ->set('form.pickup_date', $pickupDate)
            ->set('form.pickupMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_from_id', Localisation::find(1)->id)
            ->set('form.dropMode', ReservationService::WITH_PLACE)
            ->set('form.localisation_to_id', Localisation::find(2)->id)
            ->set('form.hasBack', true)
            ->set('form.reservation_back.pickup_date', $backPickUpDate)
            ->set('form.reservation_back.has_steps', true)
            ->set('form.reservation_back.steps', "test")
            // BACK PLACE FROM
            ->set('form.backPickupMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationFromBack.adresse', 'aller de test')
            ->set('form.newAdresseReservationFromBack.code_postal', '34000')
            ->set('form.newAdresseReservationFromBack.ville', 'Montpellier')
            // BACK PLACE TO
            ->set('form.backDropMode', ReservationService::WITH_NEW_ADRESSE)
            ->set('form.newAdresseReservationToBack.adresse', 'arrivée de test')
            ->set('form.newAdresseReservationToBack.code_postal', '34000')
            ->set('form.newAdresseReservationToBack.ville', 'Montpellier')
            ->call('saveReservation')
            ->assertHasNoErrors()
            ->assertRedirect(route('front.reservation.list'))        ;

        $this->assertTrue(AdresseReservation::whereAdresse('aller de test')->exists());
        $this->assertTrue(AdresseReservation::whereAdresse('arrivée de test')->exists());
        $this->assertTrue(Reservation::wherePickupDate($pickupDate)->exists());
        $this->assertTrue(Reservation::wherePickupDate($backPickUpDate)->exists());
    }
}
