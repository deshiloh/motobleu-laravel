<?php

namespace App\Listeners;

use App\Events\ReservationCanceled;
use App\Mail\PiloteDetached;
use App\Services\EventCalendar\GoogleCalendarService;
use App\Services\SentryService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationCanceledListener
{
    private GoogleCalendarService $calendarService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Handle the event.
     *
     * @param ReservationCanceled $event
     * @return void
     */
    public function handle(ReservationCanceled $event): void
    {
        try {
            $this->calendarService->deleteEvent($event->reservation);
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $event->reservation
                ])->exception($exception);
            }
            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant la suppression de l'évènement", [
                    'exception' => $exception,
                    'reservation' => $event->reservation
                ]);
            }
        }

        // Envois de l'email d'annulation de réservation.
        $recipients = [];
        if ($event->reservation->send_to_passager) {
            $recipients[] = $event->reservation->passager->email;
        }

        $recipients[] = $event->reservation->passager->user->email;

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)
                    ->send(new \App\Mail\ReservationCanceled($event->reservation));
            } catch (\Exception $exception) {
                if (App::environment(['local'])) {
                    ray([
                        'reservation' => $event->reservation
                    ])->exception($exception);
                }
                if (App::environment(['beta', 'prod'])) {
                    Log::channel('sentry')->error("Erreur pendant l'envoi de mail d'annulation", [
                        'exception' => $exception,
                        'reservation' => $event->reservation
                    ]);
                }
                continue;
            }
        }

        Mail::to($event->reservation->pilote->email)
            ->send(new PiloteDetached($event->reservation));
    }
}
