<?php

namespace Tests\Feature\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAccessPageReservation()
    {
        $response = $this->get(route('front.reservation'));

        $response->assertStatus(200);
    }
}
