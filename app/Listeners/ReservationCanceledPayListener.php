<?php

namespace App\Listeners;

use App\Events\ReservationCanceledPay;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReservationCanceledPayListener
{
    public Reservation $reservation;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param ReservationCanceledPay $event
     * @return void
     */
    public function handle(ReservationCanceledPay $event): void
    {
        try {
            $recipients = [
                $event->reservation->passager->email,
                $event->reservation->passager->user->email
            ];

            foreach ($recipients as $recipient) {
                \Mail::to($recipient)
                    ->send(new \App\Mail\ReservationCanceledPay($event->reservation));
            }
        } catch (\Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (\App::environment(['beta', 'prod'])) {
                \Log::channel('sentry')->error("Erreur pendant l'envoi de l'email", [
                    'exception' => $exception,
                    'reservation' => $event->reservation
                ]);
            }
        }
    }
}
