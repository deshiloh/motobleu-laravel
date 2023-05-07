<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Services\InvoiceService;
use Illuminate\Http\Response;

class FacturationsController extends Controller
{
    /**
     * @param Facture $facture
     * @return Response
     * @throws \Exception
     */
    public function show(Facture $facture)
    {
        return InvoiceService::generateInvoice($facture)->stream();
    }
}
