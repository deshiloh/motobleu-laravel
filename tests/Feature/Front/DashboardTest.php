<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Models\AdresseReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $user->entreprises()->attach(1);
        $user->assignRole('user');

        $this->actingAs($user);
    }

    public function testAccessDashboard()
    {
        $response = $this->get(route('front.dashboard'));

        $response->assertStatus(200);
    }

    public function testGuestNotAccess()
    {
        \Auth::logout();

        $response = $this->get(route('front.dashboard'));
        $response->assertStatus(302);
    }
}
