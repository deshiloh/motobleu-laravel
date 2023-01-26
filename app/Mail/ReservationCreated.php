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
        $subject = !$this->reservation->has_back ?
            "MOTOBLEU / Réservation " . $this->reservation->reference . " en attente de validation." :
            "MOTOBLEU / Réservation " . $this->reservation->reference . " & " . $this->reservation->reservationBack->reference . " en attente de validation.";

        return new Envelope(
            subject: $subject
        );
    }

    public function content(): Content
    {
        if ($this->sendToAdmin) {
            return new Content(
                markdown: 'emails.reservation.admin.created',
                with: [
                    'reservation' => $this->reservation
                ]
            );
        } else {
            return new Content(
                markdown: 'emails.reservation.created',
                with: [
                    'reservation' => $this->reservation
                ]
            );
        }
    }
}
