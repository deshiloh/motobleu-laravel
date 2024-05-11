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
        $reservation = $event->reservation;

        try {
            $this->calendarService->deleteEvent($reservation);
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $reservation
                ])->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant la suppression de l'évènement", [
                    'exception' => $exception,
                    'reservation' => $reservation
                ]);
            }
        }

        // Envois de l'email d'annulation de réservation.
        $recipients = [];
        if ($reservation->send_to_passager) {
            $recipients[] = $reservation->passager->email;
        }

        $recipients[] = $reservation->passager->user->email;

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)
                    ->send(new \App\Mail\ReservationCanceled($reservation));
            } catch (\Exception $exception) {
                if (App::environment(['local'])) {
                    ray([
                        'reservation' => $reservation
                    ])->exception($exception);
                }

                if (App::environment(['beta', 'prod'])) {
                    Log::channel('sentry')->error("Erreur pendant l'envoi de mail d'annulation", [
                        'exception' => $exception,
                        'reservation' => $reservation
                    ]);
                }

                continue;
            }
        }
    }
}
