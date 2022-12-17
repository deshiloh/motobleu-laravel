<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Services\EventCalendar\EventFactory;
use App\Services\EventCalendar\GoogleCalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\GoogleCalendar\Event;
use Tests\TestCase;

class GoogleCalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private GoogleCalendarService $calendarService;

    private $reservation;

    /**
     * @var EventFactory|MockObject
     */
    private EventFactory|MockObject $eventFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $event = $this->createMock(Event::class);

        $this->eventFactory = $this->createMock(EventFactory::class);
        $this->eventFactory->method("getEvent")->willReturn($event);

        $this->calendarService = new GoogleCalendarService(
            $this->eventFactory
        );

        $this->reservation = Reservation::find(1);
    }

    public function testCreateEventForMotobleuSuccess()
    {
        $res = $this->calendarService->createEventForMotobleu($this->reservation);
        $this->assertTrue($res);
    }

    public function testCreateEventForSecretarySuccess()
    {
        $res = $this->calendarService->createEventForSecretary($this->reservation);
        $this->assertTrue($res);
    }
}
