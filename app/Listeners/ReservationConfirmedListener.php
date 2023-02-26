<?php

namespace App\Listeners;

use App\Events\ReservationConfirmed;
use App\Mail\AdminReservationConfirmed;
use App\Services\SentryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationConfirmedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ReservationConfirmed $event
     * @return void
     */
    public function handle(ReservationConfirmed $event): void
    {
        try {
            if ($event->reservation->send_to_user) {
                Mail::to($event->reservation->passager->user->email)
                    ->send(new \App\Mail\ReservationConfirmed($event->reservation, false, $event->message));
            }

            if ($event->reservation->send_to_passager) {
                Mail::to($event->reservation->passager->email)
                    ->send(new \App\Mail\ReservationConfirmed($event->reservation, false, $event->message));
            }

            // Envoie à l'admin du site
            Mail::to(config('mail.admin.address'))
                ->send(new AdminReservationConfirmed($event->reservation));
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $event->reservation
                ])->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant l'envoi des emails de confirmation", [
                    'exception' => $exception,
                    'reservation' => $event->reservation
                ]);
            }
        }
    }
}
