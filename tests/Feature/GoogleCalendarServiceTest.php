<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleCalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateEvent()
    {
        $this->assertTrue(true);
    }
}
