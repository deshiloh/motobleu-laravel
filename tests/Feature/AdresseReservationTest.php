<?php

namespace Tests\Feature;

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
}
