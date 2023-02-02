<?php

namespace Tests\Feature;

use App\Http\Livewire\Facturation\EditionFacture;
use App\Mail\BillCreated;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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

    public function testCanAcessReservationsList()
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

    public function testEditReservationControl()
    {
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('entrepriseIdSelected', 1)
            ->call('editReservation', [
                'tarif' => '',
                'majoration' => 0,
                'complement' => 0,
                'comment' => '',
                'reservation' => 1
            ])
            ->assertNotEmitted('reservationUpdated')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseMissing('reservations', [
            'id' => 1,
            'tarif' => 300
        ]);
    }

    public function testEditReservation()
    {
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('entrepriseIdSelected', 1)
            ->call('editReservation', [
                'tarif' => 300,
                'majoration' => 0,
                'complement' => 0,
                'comment' => '',
                'reservation' => 1
            ])
            ->assertEmitted('reservationUpdated')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('reservations', [
            'id' => 1,
            'tarif' => 300
        ]);
    }

    public function testOpenFactureModal()
    {
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('entrepriseIdSelected', 1)
            ->set('isAcquitte', false)
            ->call('sendFactureModal')
            ->assertSet('factureModal', true)
            ->assertHasNoErrors()
            ->assertStatus(200)
        ;
    }

    public function testSendFactureWithReservationWithoutTarif()
    {
        Event::fake();

        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('entrepriseIdSelected', 1)
            ->set('factureModal', true)
            ->set('email.address', 'test@test.com')
            ->set('email.message', 'Je suis un test')
            ->call('sendFactureAction')
            ->assertSet('factureModal', true)
            ->assertHasNoErrors([
                'reservations'
            ])
            ->assertStatus(200)
        ;

        Event::assertNotDispatched(\App\Events\BillCreated::class);
    }

    public function testSendFactureSuccess()
    {
        $reservations = Reservation::where('entreprise_id', 1)->get();

        foreach ($reservations as $reservation) {
            $reservation->updateQuietly([
                'tarif' => 10
            ]);
        }

        Event::fake();

        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('entrepriseIdSelected', 1)
            ->set('factureModal', true)
            ->set('email.address', 'test@test.com')
            ->set('email.message', 'Je suis un test')
            ->call('sendFactureAction')
            ->assertSet('factureModal', false)
            ->assertHasNoErrors()
            ->assertStatus(200)
        ;

        Event::assertDispatched(\App\Events\BillCreated::class);
    }

    public function testSendEmailTest()
    {
        Mail::fake();

        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('entrepriseIdSelected', 1)
            ->set('factureModal', true)
            ->call('sendEmailTestAction')
            ->assertHasNoErrors()
            ->assertStatus(200)
        ;

        Mail::assertSent(BillCreated::class, function(Mailable $mail) {
            return $mail->hasSubject("MOTOBLEU / Votre facturation (" . Carbon::now()->month . " / " . Carbon::now()->year . ")");
        });
    }
}
