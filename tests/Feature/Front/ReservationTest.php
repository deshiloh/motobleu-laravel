<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Reservation\ReservationDataTable;
use App\Mail\CancelReservationDemand;
use App\Mail\UpdateReservationDemand;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * @return void
     */
    public function testAccessPageReservation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.reservation.list'));

        $response->assertStatus(200);
    }

    public function testGuestNotAccess()
    {
        \Auth::logout();

        $response = $this->get(route('front.reservation.list'));
        $response->assertStatus(302);
    }

    public function testGuestNotAccessCreate()
    {
        \Auth::logout();

        $response = $this->get(route('front.reservation.create'));
        $response->assertStatus(302);
    }

    public function testSendEditEmailUpdateReservationWithErrors()
    {
        Mail::fake();

        Livewire::test(ReservationDataTable::class)
            ->call('sendUpdateReservationEmail')
            ->assertHasErrors([
                'message' => "required",
                'selectedReservation' => 'required'
            ])
        ;

        Mail::assertNothingSent();
    }

    public function testSendEditEmailUpdateReservation()
    {
        Mail::fake();

        Livewire::test(ReservationDataTable::class)
            ->set('selectedReservation', Reservation::factory()->create())
            ->set('message', "Ceci est un message")
            ->call('sendUpdateReservationEmail')
            ->assertHasNoErrors()
        ;

        Mail::assertSent(UpdateReservationDemand::class);
    }

    public function testCancelEditDemandUpdateReservation()
    {
        Livewire::test(ReservationDataTable::class)
            ->set('selectedReservation', Reservation::factory()->create())
            ->set('message', "Ceci est un message")
            ->call('closeModal')
            ->assertHasNoErrors()
            ->assertSet('selectedReservation', null)
            ->assertSet('message', null)
            ->assertSet('editAskCard', false)
        ;
    }

    public function testSendCancelReservationEmail()
    {
        Mail::fake();

        $this->actingAs(User::factory()->create());

        Livewire::test(ReservationDataTable::class)
            ->call('sendCancelReservationEmail')
            ->assertHasNoErrors()
            ->assertSet('selectedReservation', null)
            ->assertSet('askCancelCard', false)
        ;

        Mail::assertSent(CancelReservationDemand::class);
    }
}
