<?php

namespace Tests\Feature;

use App\Enum\BillStatut;
use App\Enum\ReservationStatus;
use App\Events\BillCreated;
use App\Http\Livewire\Facturation\EditionFacture;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Livewire;
use Tests\TestCase;

class FacturationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testCanAccessEditionFacturePage()
    {
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->assertHasNoErrors()
            ->assertStatus(200);
    }

    public function testCanAccessReservationsList()
    {
        $reservation = Reservation::where('entreprise_id', 1)->first();

        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->call('goToEditPage', 1)
            ->assertHasNoErrors()
            ->assertSee($reservation->reference)
            ->assertStatus(200);
    }

    public function testCanAccessFactureReservations()
    {
        /** @var USer $user */
        $user = User::find(1);
        $user->assignRole('super admin');
        $this->actingAs($user);

        $reservation = Reservation::where('entreprise_id', 1)->first();
        $facture = Facture::factory()->create();
        $reservation->updateQuietly([
            'facture_id' => $facture->id
        ]);

        $response = $this->get(route('admin.facturations.edition', [
            'selectedMonth' => Carbon::now()->month,
            'selectedYear' => Carbon::now()->year,
            'factureSelected' => $facture->id
        ]));

        $response->assertStatus(200);
        $response->assertSee($facture->reference);
    }

    public function testAddTarifReservation()
    {
        Livewire::test(EditionFacture::class)
            ->call('editReservation', [
                'reservation' => 1,
                'tarif' => 200,
                'majoration' => '',
                'complement' => '',
                'comment_facture' => ''
            ])
            ->assertHasNoErrors()
            ->assertEmitted('reservationUpdated')
            ->assertDispatchedBrowserEvent('wireui:notification')
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('reservations', [
            'id' => 1,
            'tarif' => 200
        ]);
    }

    public function testAddTarifGiveError()
    {
        Livewire::test(EditionFacture::class)
            ->call('editReservation', [
                'reservation' => 1,
                'tarif' => '',
                'majoration' => '',
                'complement' => '',
                'comment_facture' => ''
            ])
            ->assertNotEmitted('reservationUpdated')
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;
    }

    public function testAcquitteFacture()
    {
        $facture = Facture::find(1);

        $reservation = Reservation::find(1);
        $reservation->updateQuietly([
            'facture_id' => $facture->id,
            'tarif' => 200
        ]);
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->call('updateAcquitteBill')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertDatabaseHas('factures', [
            'reference' => $facture->reference,
            'is_acquitte' => 1
        ]);
    }

    public function testSendFacture()
    {
        Event::fake();

        $facture = Facture::find(1);

        $reservation = Reservation::find(1);
        $reservation->updateQuietly([
            'facture_id' => $facture->id,
            'tarif' => 200
        ]);
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('email.address', 'test@test.com')
            ->set('email.message', 'contenu du message')
            ->call('sendFactureAction')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
            ->assertSet('isSendFactureModalOpened', false)
        ;

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'statut' => ReservationStatus::Billed->value
        ]);

        $this->assertDatabaseHas('factures', [
            'reference' => $facture->reference,
            'statut' => BillStatut::COMPLETED->value
        ]);

        Event::assertDispatched(BillCreated::class);
    }

    public function testSendEmailTest()
    {
        \Mail::fake();

        $entreprise = Entreprise::find(1);
        $facture = Facture::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->set('email.message', 'contenu du message')
            ->call('sendEmailTestAction')
            ->assertHasNoErrors()
            ->assertStatus(200)
        ;

        \Mail::assertSent(\App\Mail\BillCreated::class);
    }

    public function testGetEntrepriseProperty()
    {
        $facture = Facture::find(1);
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->call('getEntrepriseProperty')
            ->assertSet('entreprise', $entreprise)
            ->assertStatus(200);
    }

    public function testGenerateFacture()
    {
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', null)
            ->call('generateFacture', 1)
            ->assertStatus(200);
    }

    public function testUpdateReservation()
    {
        $facture = Facture::find(1);
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->call('reservationUpdated')
            ->assertStatus(200);
    }

    public function testOpenSendFactureModal()
    {
        $facture = Facture::find(1);
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->call('openSendFactureModal')
            ->assertSet('isSendFactureModalOpened', true)
            ->assertStatus(200);
    }

    public function testEditFactureAction()
    {
        $facture = Facture::find(1);
        $entreprise = Entreprise::find(1);

        Livewire::test(EditionFacture::class)
            ->set('entreprise', $entreprise)
            ->set('facture', $facture)
            ->set('email.complement', 'test')
            ->call('editFactureAction')
            ->assertStatus(200);
        $this->assertDatabaseHas('factures', [
            'id' => $facture->id,
            'information' => 'test'
        ]);
    }

    #[NoReturn]
    public function testGenerateReferenceAfterYear2024()
    {
        Facture::factory(10)->create([
            'month' => 01,
            'year' => 2024,
        ]);

        Facture::factory(10)->create([
            'month' => 02,
            'year' => 2024,
        ]);

        \Date::setTestNow(Carbon::create(2024, 1, 1, 0, 0, 0));
        $reference = Facture::generateReference('2024', '03');

        $this->assertEquals('FA2024-03-031', $reference);
    }

    #[NoReturn]
    public function testGenerateReferenceBefore2024()
    {
        Facture::factory(10)->create([
            'month' => 2,
            'year' => 2023,
        ]);

        Facture::factory(2)->create([
            'month' => 3,
            'year' => 2023,
        ]);

        \Date::setTestNow(Carbon::create(2023, 12, 2, 0, 0, 0));
        $reference = Facture::generateReference('2023', '02');

        $this->assertEquals('FA2023-02-11', $reference);
    }
}
