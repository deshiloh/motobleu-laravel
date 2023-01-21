<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MOTOBLEU / Réservation ' . $this->reservation->reference . ' a été confirmé'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation.admin.confirmed'
        );
    }
}
