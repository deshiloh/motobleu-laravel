<?php

namespace App\Services\EventCalendar;

use Spatie\GoogleCalendar\Event;

class EventFactory
{
    public function getEvent(?string $evenId): Event
    {
        return (is_null($evenId)) ? new Event() : Event::find($evenId);
    }
}
