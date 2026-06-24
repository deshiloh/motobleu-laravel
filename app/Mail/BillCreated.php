<?php

namespace App\Mail;

use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Services\PennylaneService;
use app\Settings\BillSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

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

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MOTOBLEU / Votre facturation (" . sprintf('%02d', $this->facture->month) . " / " . $this->facture->year . ")"
        );
    }

    /**
     * @return Attachment[]
     * @throws BindingResolutionException
     */
    public function attachments(): array
    {
        $billSettings = app(BillSettings::class);
        $attachments = [];
        $entreprise = $this->getEntreprise();

        // Attach invoice PDF from Pennylane if the sync succeeded
        if ($this->facture->pennylane_invoice_id) {
            try {
                $pdfContent = app(PennylaneService::class)
                    ->getInvoicePdf($this->facture->pennylane_invoice_id);

                $attachments[] = Attachment::fromData(
                    fn() => $pdfContent,
                    $this->facture->invoice_number . '.pdf'
                )->withMime('application/pdf');
            } catch (\Throwable) {
                // If PDF fetch fails, continue without the invoice attachment
            }
        }

        // Reservation recap (XLS or PDF) — unchanged
        if (in_array($entreprise->id, $billSettings->entreprises_xls_file)) {
            $excel = Excel::raw(new ReservationsExport(
                $this->facture->year,
                $this->facture->month,
                $entreprise,
                $this->facture->id
            ), \Maatwebsite\Excel\Excel::XLSX);

            $attachments[] = Attachment::fromData(fn() => $excel, $this->getFileName() . '.xlsx')
                ->withMime('application/xlsx');

        } else {
            $pdfData = Pdf::loadView('exports.reservations.pdf-facture', [
                'entreprise' => $entreprise,
                'year' => $this->facture->year,
                'month' => $this->facture->month,
                'factureSelected' => $this->facture,
            ])->output();

            $attachments[] = Attachment::fromData(fn() => $pdfData, $this->getFileName() . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }

    private function getEntreprise(): Entreprise
    {
        return $this->facture->reservations()->get()->first()->entreprise;
    }

    private function getFileName(): string
    {
        return sprintf('courses_periode_%s_%s',
            $this->facture->month,
            $this->facture->year
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.bill.created',
            with: [
                'message' => $this->message,
            ]
        );
    }
}
