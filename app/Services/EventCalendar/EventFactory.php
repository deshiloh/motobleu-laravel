<?php

namespace App\Services\EventCalendar;

use Illuminate\Support\Facades\App;
use Spatie\GoogleCalendar\Event;

class EventFactory
{
    public function getEvent(?string $evenId)
    {
        // Return mock object in testing environment
        if (App::environment('testing')) {
            return $this->createMockEvent($evenId);
        }

        return (is_null($evenId)) ? new Event() : Event::find($evenId);
    }

    private function createMockEvent(?string $eventId = null)
    {
        return new class($eventId) {
            public $id;
            public $name = '';
            public $description = '';
            public $startDateTime = null;
            public $endDateTime = null;
            public $location = '';

            public function __construct($id = null) {
                $this->id = $id ?: 'test-event-id';
            }

            public function save($calendar = null, $options = []) {
                return $this;
            }

            public function delete($calendar = null, $options = []) {
                return true;
            }

            public function addAttendee($attendee) {
                return $this;
            }
        };
    }
}
