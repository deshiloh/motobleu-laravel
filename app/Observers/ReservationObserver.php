<?php

namespace App\Observers;

use App\Enum\ReservationStatus;
use App\Mail\ReservationCreated;
use App\Models\Reservation;
use App\Services\EventCalendar\GoogleCalendarService;
use Illuminate\Support\Facades\App;
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

            if (!empty($reservation->passager->user->email)) {
                $recipients[] = $reservation->passager->user->email;
            }

            if ($reservation->send_to_passager && !empty($reservation->passager->email)) {
                $recipients[] = $reservation->passager->email;
            }

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new ReservationCreated($reservation, false));
            }

            // Envoi de l'email à l'admin
            Mail::to(config('mail.admin.address'))->send(new ReservationCreated($reservation, true));

            $this->calendarService->createEventForMotobleu($reservation);
            $this->calendarService->createEventForSecretary($reservation);
        }catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $reservation
                ])->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant la génération Google Calendar", [
                    'exception' => $exception,
                    'reservation' => $reservation
                ]);
            }
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
        if ($reservation->statut->value < ReservationStatus::Billed->value &&
            $reservation->encaisse_pilote > 0 &&
            $reservation->facture_id > 0
        ) {
            $facture = $reservation->facture;

            $reservation->facture()->disassociate();

            $reservation->tarif = null;
            $reservation->majoration = null;
            $reservation->complement = null;

            $reservation->updateQuietly();

            $facture->refresh();

            if($facture->reservations->isEmpty()) {
                $facture->delete();
            }
        }

        try {
            $this->calendarService->createEventForMotobleu($reservation);
            $this->calendarService->createEventForSecretary($reservation);
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant la génération Google Calendar", [
                    'exception' => $exception,
                    'reservation' => $reservation
                ]);
            }
        }
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
