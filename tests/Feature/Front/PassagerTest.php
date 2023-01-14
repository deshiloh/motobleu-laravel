<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Models\AdresseReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PassagerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /**
     * @return void
     */
    public function testAccessAddressList(): void
    {
        $response = $this->get(route('front.passager.list'));

        $response->assertStatus(200);
    }

    public function testAccessCreatePage()
    {
        $response = $this->get(route('front.passager.create'));
        $response->assertStatus(200);
    }
}
