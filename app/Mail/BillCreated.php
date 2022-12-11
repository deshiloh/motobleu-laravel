<?php

namespace App\Mail;

use App\Exports\ReservationsExport;
use App\Models\Facture;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
     * @throws Exception
     */
    public function build(): Mailable
    {
        $invoice = InvoiceService::generateInvoice($this->facture);

        $mailable = $this->markdown('emails.bill.created', [
            'message' => $this->message
        ])->attachData($invoice->stream()->getContent(), $this->facture->reference.'.pdf', [
            'mime' => 'application/pdf',
        ]);

        if (true) {
            $excel = Excel::raw(new ReservationsExport(
                $this->facture->year,
                $this->facture->month,
                $this->facture->reservations()->get()->first()->entreprise
            ), \Maatwebsite\Excel\Excel::XLSX);

            $fileName = sprintf('courses_periode_%s_%s',
                $this->facture->month,
                $this->facture->year
            );

            $mailable->attachData($excel, $fileName . '.xlsx');
        }

        return $mailable;

    }
}
