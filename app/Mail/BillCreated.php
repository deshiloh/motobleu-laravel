<?php

namespace App\Mail;

use App\Models\Facture;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillCreated extends Mailable
{
    use Queueable, SerializesModels;

    private Facture $facture;
    private string $data;
    private string $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Facture $facture, string $message)
    {
        $this->facture = $facture;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        $invoice = InvoiceService::generateInvoice($this->facture);

        return $this->markdown('emails.bill.created', [
            'message' => $this->message
        ])
            ->attachData($invoice->stream()->getContent(), $this->facture->reference.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
