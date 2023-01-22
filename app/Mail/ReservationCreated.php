<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationCreated extends Mailable
{
    use Queueable, SerializesModels;

    private Reservation $reservation;
    private bool $sendToAdmin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, bool $sendToAdmin = false)
    {
        $this->reservation = $reservation;
        $this->sendToAdmin = $sendToAdmin;
    }

    public function envelope(): Envelope
    {
        // TODO Réservation retour
        return new Envelope(
            subject: "MOTOBLEU / Réservation " . $this->reservation->reference . " en attente de validation."
        );
    }

    public function content(): Content
    {
        if ($this->sendToAdmin) {
            return new Content(
                markdown: 'emails.reservation.admin-created',
                with: [
                    'reservation' => $this->reservation
                ]
            );
        } else {
            // TODO Changer le contenu
            return new Content(
                markdown: 'emails.reservation.created',
                with: [
                    'reservation' => $this->reservation
                ]
            );
        }
    }
}
