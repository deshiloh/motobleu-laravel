<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Services\InvoiceService;
use App\Services\PennylaneService;
use Illuminate\Http\Response;

class FacturationsController extends Controller
{
    /**
     * @param Facture $facture
     * @return Response|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function show(Facture $facture)
    {
        // Fallback for invoices created before Pennylane integration
        if (!$facture->pennylane_invoice_id) {
            return InvoiceService::generateInvoice($facture)->stream();
        }

        $pennylane = app(PennylaneService::class);

        // Admin → redirect to Pennylane PDF URL (fresh signed URL, valid 30 min)
        if (request()->routeIs('admin.facturations.show')) {
            return redirect()->away(
                $pennylane->getInvoiceUrl($facture->pennylane_invoice_id)
            );
        }

        // Front (client) → stream PDF downloaded from Pennylane
        $pdf = $pennylane->getInvoicePdf($facture->pennylane_invoice_id);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $facture->invoice_number . '.pdf"',
        ]);
    }
}
