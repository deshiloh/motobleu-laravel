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
    private string $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        Reservation $reservation,
        bool $sendToAdmin = true,
        string $message = ''
    ) {
        $this->reservation = $reservation;
        $this->sendToAdmin = $sendToAdmin;
        $this->message = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MOTOBLEU / Réservation N° " . $this->reservation->reference . " confirmée"
        );
    }

    public function content(): Content
    {
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
                    'message' => $this->message
                ]
            );
        }
    }
}
