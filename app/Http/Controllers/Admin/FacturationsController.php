<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Services\InvoiceService;

class FacturationsController extends Controller
{
    public function show(Facture $facture)
    {
        return InvoiceService::generateInvoice($facture)->stream();

        //Mail::to('test@test.com')->send(new BillCreated($facture, $invoice->stream()));
    }
}
