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
            if ($event->reservation->send_to_passager && !empty($event->reservation->passager->email)) {
                Mail::to($event->reservation->passager->email)
                    ->send(new \App\Mail\ReservationConfirmed($event->reservation, false, $event->message));
            }
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $event->reservation
                ])->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant l'envoi de l'email de confirmation au passager", [
                    'exception' => $exception,
                    'reservation' => $event->reservation,
                    'email' => $event->reservation->passager->email
                ]);
            }
        }

        try {
            // Envoi de l'email à la secrétaire
            Mail::to($event->reservation->passager->user->email)
                ->send(new \App\Mail\ReservationConfirmed($event->reservation, false, $event->message));
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $event->reservation
                ])->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant l'envoi de l'email de confirmation à la secrétaire", [
                    'exception' => $exception,
                    'reservation' => $event->reservation,
                    'email' => $event->reservation->passager->user->email
                ]);
            }
        }

        try {
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
                Log::channel('sentry')->error("Erreur pendant l'envoi de l'email de confirmation à l'administrateur", [
                    'exception' => $exception,
                    'reservation' => $event->reservation,
                    'email' => config('mail.admin.address')
                ]);
            }
        }
    }
}
