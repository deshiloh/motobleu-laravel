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
            // Suppression de l'évènement dans le calendrier
            $this->calendarService->deleteEvent($event->reservation);
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $event->reservation
                ])->exception($exception);
            }
            // TODO Sentry en production
        }

        // Envois de l'email d'annulation de réservation.
        $recipients = [];
        if ($event->reservation->send_to_passager) {
            $recipients[] = $event->reservation->passager->email;
        }

        if ($event->reservation->send_to_user) {
            $recipients[] = $event->reservation->passager->user->email;
        }

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
                // TODO Sentry en prodduction
            }
        }

        Mail::to($event->reservation->pilote->email)
            ->send(new PiloteDetached($event->reservation));

        Log::channel('logtail')->info("Annulation d'une réservation", [
            'utilisateur' => Auth::user(),
            'reservation' => $event->reservation,
        ]);
    }
}
