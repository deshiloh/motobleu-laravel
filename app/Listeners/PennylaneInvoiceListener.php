<?php

namespace App\Listeners;

use App\Events\BillCreated;
use App\Services\PennylaneService;
use Illuminate\Support\Facades\Log;

class PennylaneInvoiceListener
{
    public function __construct(private PennylaneService $pennylane) {}

    public function handle(BillCreated $event): void
    {
        $facture = $event->facture;

        try {
            $customerId = $facture->pennylane_customer_id
                ?? $this->pennylane->syncCustomer($facture);

            if (!$facture->pennylane_customer_id) {
                $facture->updateQuietly(['pennylane_customer_id' => (string) $customerId]);
            }

            if (!$facture->pennylane_invoice_id) {
                $invoice = $this->pennylane->createInvoice($facture, (int) $customerId);
                $facture->updateQuietly([
                    'pennylane_invoice_id'     => $invoice['id'],
                    'pennylane_invoice_number' => $invoice['invoice_number'],
                ]);
            }

            // Refresh so BillCreatedListener reads the updated pennylane fields
            $event->facture->refresh();

        } catch (\Throwable $e) {
            Log::error('Pennylane sync failed', [
                'facture_id' => $facture->id,
                'message'    => $e->getMessage(),
            ]);
            // Do not rethrow — email sending must proceed even if Pennylane is unavailable
        }
    }
}
