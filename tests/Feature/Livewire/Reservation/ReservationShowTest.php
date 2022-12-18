<?php

namespace Tests\Feature\Livewire\Reservation;

use App\Http\Livewire\Reservation\ReservationShow;
use App\Models\Pilote;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Livewire\Livewire;
use Tests\TestCase;

class ReservationShowTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected bool $seed = true;

    /** @test */
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

    public function testReservationConfirmOK(): void
    {
        $reservation = Reservation::find(1);
        Livewire::test(ReservationShow::class, ['reservation' => $reservation])
            ->set('reservation.pilote_id', Pilote::find(1)->id)
            ->call('confirmedAction')
            ->assertHasNoErrors()
        ;
        $this->assertTrue(Reservation::where('is_confirmed', true)->exists());
    }

    public function testReservationCancelOK(): void
    {
        $reservation = Reservation::find(1);
        Livewire::test(ReservationShow::class, ['reservation' => $reservation])
            ->call('cancelAction')
            ->assertHasNoErrors()
        ;
        $this->assertTrue(Reservation::where([
            ['is_confirmed', '=', false],
            ['is_cancel', '=', true]
        ])->exists());
    }
}
