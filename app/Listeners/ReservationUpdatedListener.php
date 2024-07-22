<?php

namespace App\Listeners;

use App\Events\ReservationUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReservationUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReservationUpdated $event): void
    {
        $reservation = $event->reservation;

        $contacts = [];

        if (null !== $reservation->passager) {
            $contacts[] = $reservation->passager->user->email;
        }

        ray($reservation->send_to_passager);

        if ($reservation->send_to_passager) {
            $contacts[] = $reservation->passager->email;
        }

        foreach ($contacts as $contact) {
            try {
                \Mail::to($contact)
                    ->send(new \App\Mail\ReservationUpdated($reservation));
            } catch (\Exception $exception) {
                if (\App::environment(['local'])) {
                    ray()->exception($exception);
                }

                if (\App::environment(['prod', 'beta'])) {
                    \Log::channel('sentry')->error('Erreur lors de l\'envoi des messages de mise à jour', [
                        'exception' => $exception,
                        'reservation' => $reservation,
                    ]);
                }
            }
        }
    }
}
