<?php

namespace App\Observers;

use App\Mail\ReservationCreated;
use App\Models\Reservation;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationObserver
{
    private GoogleCalendarService $calendarService;

    public bool $afterCommit = true;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Handle the Reservation "created" event.
     *
     * @param Reservation $reservation
     * @return void
     */
    public function created(Reservation $reservation): void
    {
        try {
            $recipients = [];

            if ($reservation->send_to_passager) {
                $recipients[] = $reservation->passager->email;
            }

            if ($reservation->send_to_user) {
                $recipients[] = $reservation->passager->user->email;
            }

            foreach ($recipients as $recipient) {
                // Envois au passager et secrétaire si sélectionnés
                Mail::to($recipient)->send(new ReservationCreated($reservation));
            }

            $this->calendarService->createEventForMotobleu($reservation);
            $this->calendarService->createEventForSecretary($reservation);

            Log::channel('logtail')->info("Création d'une réservation", [
                'utilisateur' => Auth::user(),
                'reservation' => $reservation,
            ]);
        }catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $reservation
                ])->exception($exception);
            }

            // TODO Sentry en production
        }
    }

    /**
     * Handle the Reservation "updated" event.
     *
     * @param Reservation $reservation
     * @return void
     */
    public function updated(Reservation $reservation): void
    {
        $this->calendarService->createEventForMotobleu($reservation);
        $this->calendarService->createEventForSecretary($reservation);
    }

    /**
     * Handle the Reservation "deleted" event.
     *
     * @param Reservation $reservation
     * @return void
     */
    public function deleted(Reservation $reservation)
    {
        //
    }

    /**
     * Handle the Reservation "restored" event.
     *
     * @param Reservation $reservation
     * @return void
     */
    public function restored(Reservation $reservation)
    {
        //
    }

    /**
     * Handle the Reservation "force deleted" event.
     *
     * @param Reservation $reservation
     * @return void
     */
    public function forceDeleted(Reservation $reservation)
    {
        //
    }
}
