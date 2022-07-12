<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Facturation\EditionFacture;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->call('goToEditPage', 1)
            ->assertHasNoErrors()
            ->assertStatus(200);
    }

    public function testOpenModalReservation()
    {
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->call('reservationModal', 1)
            ->assertHasNoErrors()
            ->assertStatus(200);
    }

    public function testEditReservation()
    {
        Livewire::test(EditionFacture::class)
            ->set('selectedMonth', Carbon::now()->month)
            ->set('selectedYear', Carbon::now()->year)
            ->set('reservationSelected', 1)
            ->set('reservationFormData.tarif', 300)
            ->call('saveReservationAction')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertTrue(Reservation::where('tarif', 300)->exists());
    }
}
