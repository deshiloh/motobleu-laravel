<?php

namespace App\Mail;

use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Services\InvoiceService;
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
            subject: "MOTOBLEU / Votre facturation (" . $this->facture->month . " / " . $this->facture->year . ")"
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
        $invoice = InvoiceService::generateInvoice($this->facture);
        $entreprise = $this->getEntreprise();

        $attachments[] = Attachment::fromData(
            fn() => $invoice->stream()->getContent(),
            $this->facture->reference.'.pdf'
        )->withMime('application/pdf');

        if (in_array($entreprise->id, $billSettings->entreprises_xls_file)) {
            $excel = Excel::raw(new ReservationsExport(
                $this->facture->year,
                $this->facture->month,
                $entreprise
            ), \Maatwebsite\Excel\Excel::XLSX);

            $attachments[] = Attachment::fromData(fn() => $excel, $this->getFileName() . '.xlsx')
                ->withMime('application/xlsx');

        } else {
            $pdfData = Pdf::loadView('exports.reservations.pdf-facture', [
                'entreprise' => $entreprise,
                'year' => $this->facture->year,
                'month' => $this->facture->month
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
                'message' => $this->message
            ]
        );
    }
}
