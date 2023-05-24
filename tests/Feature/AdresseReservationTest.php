<?php

namespace Tests\Feature;

use App\Http\Livewire\Reservation\AdressesReservationDataTable;
use App\Models\AdresseReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdresseReservationTest extends TestCase
{
    use RefreshDatabase;

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

    public function testAcessList()
    {
        $response = $this->get(route('admin.adresse-reservation.index'));
        $response->assertStatus(200);
    }

    public function testAcessCreateForm()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('admin.adresse-reservation.create'));
        $response->assertStatus(200);
    }

    public function testSearchAdresseReservation()
    {
        \Livewire::test(AdressesReservationDataTable::class)
            ->set('search', 'test')
            ->assertHasNoErrors()
            ->assertStatus(200);
    }

    public function testDisableAddressReservation()
    {
        $address = AdresseReservation::find(1);

        \Livewire::test(AdressesReservationDataTable::class)
            ->call('disableAddress', $address)
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('adresse_reservations', [
            'adresse' => $address->adresse,
            'is_actif' => 0
        ]);
    }

    public function testEnableAddressReservation()
    {
        $address = AdresseReservation::factory([
            'adresse' => 'test',
            'user_id' => 1,
            'is_actif' => false
        ])->create();

        \Livewire::test(AdressesReservationDataTable::class)
            ->call('enableAddress', $address)
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('adresse_reservations', [
            'adresse' => 'test',
            'is_actif' => true
        ]);
    }

    public function testSoftDeleteAddressReservation()
    {
        $address = AdresseReservation::factory([
            'adresse' => 'test',
            'user_id' => 1,
            'is_actif' => false
        ])->create();

        \Livewire::test(AdressesReservationDataTable::class)
            ->call('toggleDeleteAddress', $address)
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('adresse_reservations', [
            'adresse' => 'test',
            'is_deleted' => true
        ]);
    }
}
