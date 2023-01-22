<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    private Reservation $reservation;
    private bool $sendToAdmin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, bool $sendToAdmin = true)
    {
        $this->reservation = $reservation;
        $this->sendToAdmin = $sendToAdmin;
    }

    public function envelope(): Envelope
    {
        // TODO Aller retour
        return new Envelope(
            subject: "MOTOBLEU / Réservation N° " . $this->reservation->reference . " confirmée"
        );
    }

    public function content(): Content
    {
        // TODO Changer le contenu non admin
        if ($this->sendToAdmin) {
            return new Content(
                markdown: 'emails.reservation.admin-confirmed',
                with: [
                    'reservation' => $this->reservation
                ]
            );
        } else {
            return new Content(
                markdown: 'emails.reservation.confirmed',
                with: [
                    'reservation' => $this->reservation
                ]
            );
        }
    }
}
