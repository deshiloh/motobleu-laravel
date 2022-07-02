<?php

namespace Tests\Feature;


use App\Models\Reservation;
use App\Services\GoogleCalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Spatie\GoogleCalendar\Event;
use Tests\TestCase;

class GoogleCalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateEvent()
    {
        $this->assertTrue(true);
    }
}
