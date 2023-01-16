<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FrontTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAccessHomePage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


}
