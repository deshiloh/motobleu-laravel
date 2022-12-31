<?php

namespace App\Mail;

use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\PDF;
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
        /** @var Entreprise $entreprise */
        $entreprise = $this->facture->reservations()->get()->first()->entreprise;

        // Génération de la facture en PDF
        $invoice = InvoiceService::generateInvoice($this->facture);

        $mailable = $this->markdown('emails.bill.created', [
            'message' => $this->message
        ])->attachData($invoice->stream()->getContent(), $this->facture->reference.'.pdf', [
            'mime' => 'application/pdf',
        ]);

        $fileName = sprintf('courses_periode_%s_%s',
            $this->facture->month,
            $this->facture->year
        );

        if (in_array($entreprise->nom, config('motobleu.export.entrepriseEnableForXlsExport'))) {
            // Génération du récap des courses en xlsx
            $excel = Excel::raw(new ReservationsExport(
                $this->facture->year,
                $this->facture->month,
                $entreprise
            ), \Maatwebsite\Excel\Excel::XLSX);

            $fileName = sprintf('courses_periode_%s_%s',
                $this->facture->month,
                $this->facture->year
            );

            $mailable->attachData($excel, $fileName . '.xlsx');
        } else {
            // Génération du récap des courses en PDF
            $recapInPDF =  \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.reservations.pdf-facture', [
                'entreprise' => $entreprise,
                'year' => $this->facture->year,
                'month' => $this->facture->month
            ])->output();

            $mailable->attachData($recapInPDF, $fileName . '.pdf');
        }

        return $mailable;

    }
}
