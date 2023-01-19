<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Models\AdresseReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);
    }

    /**
     * @return void
     */
    public function testAccessAddressList(): void
    {
        $response = $this->get(route('front.address.list'));

        $response->assertStatus(200);
    }

    public function testAcessEditPage()
    {
        $response = $this->get(route('front.address.edit', ['address' => AdresseReservation::find(1)]));

        $response->assertStatus(200);
    }

    public function testAccessCreatePage()
    {
        $response = $this->get(route('front.address.create'));

        $response->assertStatus(200);
    }

    public function testSaveWithErrors()
    {
        Livewire::test(AddressForm::class)
            ->set('adresseReservation.adresse', '')
            ->set('adresseReservation.adresse_complement', '')
            ->set('adresseReservation.code_postal', '')
            ->set('adresseReservation.ville', '')
            ->call('save')
            ->assertHasErrors([
                'adresseReservation.adresse' => 'required',
                'adresseReservation.code_postal' => 'required',
                'adresseReservation.ville' => 'required',
            ]);
    }

    public function testSaveSuccess()
    {
        Livewire::test(AddressForm::class)
            ->set('adresseReservation.adresse', 'test')
            ->set('adresseReservation.adresse_complement', 'test')
            ->set('adresseReservation.code_postal', 'test')
            ->set('adresseReservation.ville', 'test')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(AdresseReservation::where('adresse', 'test')->exists());
    }

    public function testDisableAddress()
    {
        /** @var AdresseReservation $address */
        $address = AdresseReservation::factory([
            'is_actif' => true
        ])->create();

        Livewire::test(AddressDataTable::class)
            ->call('toggleAddress', $address)
            ->assertHasNoErrors();

        $this->assertTrue($address->is_actif == false);
    }

    public function testEnableAddress()
    {
        /** @var AdresseReservation $address */
        $address = AdresseReservation::factory([
            'is_actif' => false
        ])->create();

        Livewire::test(AddressDataTable::class)
            ->call('toggleAddress', $address)
            ->assertHasNoErrors();

        $this->assertTrue($address->is_actif == true);
    }

    public function testDeleteAddress()
    {
        /** @var AdresseReservation $address */
        $address = AdresseReservation::factory([
            'is_deleted' => false
        ])->create();

        Livewire::test(AddressDataTable::class)
            ->call('deleteAddress', $address)
            ->assertHasNoErrors()
        ;

        self::assertTrue($address->is_deleted == true);
    }
}
