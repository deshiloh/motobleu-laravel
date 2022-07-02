<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
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

        $user = User::find(1);
        $this->actingAs($user);
    }

    public function testAcessListReservationPage()
    {
        $response = $this->get(route('admin.reservations.index'));
        $response->assertStatus(200);
    }

    public function testAcessCreateReservationForm()
    {
        $response = $this->get(route('admin.reservations.create'));
        $response->assertStatus(200);
    }
}
