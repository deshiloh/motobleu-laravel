<?php

namespace App\Services;

use App\Models\Entreprise;
use App\Models\Facture;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PennylaneService
{
    private function http()
    {
        return Http::baseUrl(config('services.pennylane.base_url'))
            ->withToken(config('services.pennylane.api_key'))
            ->acceptJson()
            ->asJson();
    }

    /**
     * Creates or finds the Pennylane company customer for the entreprise linked to this facture.
     * Idempotent: uses `reference` as a stable identifier, searches before creating.
     * Returns the Pennylane integer customer ID.
     *
     * @throws RequestException
     */
    public function syncCustomer(Facture $facture): int
    {
        $entreprise = $this->getEntreprise($facture);
        $billedAddress = $entreprise->getBilledAddress();
        $reference = 'motobleu_entreprise_' . $entreprise->id;

        // Search for existing customer by our stable reference
        $search = $this->http()->get('/customers', [
            'filter' => json_encode([
                ['field' => 'external_reference', 'operator' => 'eq', 'value' => $reference],
            ]),
        ])->throw();

        $items = $search->json('items', []);
        if (!empty($items)) {
            return $items[0]['id'];
        }

        // Create company customer
        $payload = [
            'name'            => $entreprise->nom,
            'external_reference' => $reference,
            'emails'          => $billedAddress ? [$billedAddress->email] : [],
            'billing_address' => [
                'address'        => $billedAddress?->adresse ?? '',
                'postal_code'    => $billedAddress?->code_postal ?? '',
                'city'           => $billedAddress?->ville ?? '',
                'country_alpha2' => 'FR',
            ],
        ];

        if ($billedAddress?->tva) {
            $payload['vat_number'] = $billedAddress->tva;
        }

        $response = $this->http()
            ->post('/company_customers', $payload)
            ->throw();

        return $response->json('id');
    }

    /**
     * Creates a finalized invoice in Pennylane for the given facture.
     * Returns ['id' => int, 'invoice_number' => string].
     *
     * @throws RequestException
     */
    public function createInvoice(Facture $facture, int $pennylaneCustomerId): array
    {
        $billedAt = $facture->billed_at ?? $facture->created_at;

        $payload = [
            'customer_id'  => $pennylaneCustomerId,
            'date'         => $billedAt->toDateString(),
            'deadline'     => $billedAt->copy()->addDays(30)->toDateString(),
            'currency'     => 'EUR',
            'invoice_lines' => [
                [
                    'label'                   => sprintf(
                        'Transports pour la période : %02d/%d',
                        $facture->month,
                        $facture->year
                    ),
                    'quantity'                => 1,
                    'raw_currency_unit_price' => (string) round($facture->montant_ht, 6),
                    'vat_rate'                => 'FR_100', // 10% TVA
                    'unit'                    => 'piece',
                ],
            ],
        ];

        if ($facture->information) {
            $payload['pdf_description'] = $facture->information;
        }

        $data = $this->http()
            ->post('/customer_invoices', $payload)
            ->throw()
            ->json();

        return [
            'id'             => $data['id'],
            'invoice_number' => $data['invoice_number'],
        ];
    }

    /**
     * Downloads the raw PDF bytes for a Pennylane invoice.
     * Fetches a fresh public_file_url from the API (URL expires after 30 min).
     *
     * @throws RequestException
     */
    public function getInvoicePdf(int|string $pennylaneInvoiceId): string
    {
        $url = $this->fetchPublicFileUrl($pennylaneInvoiceId);

        return Http::get($url)->throw()->body();
    }

    /**
     * Returns a fresh public URL for the invoice PDF (valid 30 minutes).
     * Use for short-lived redirects only.
     *
     * @throws RequestException
     */
    public function getInvoiceUrl(int|string $pennylaneInvoiceId): string
    {
        return $this->fetchPublicFileUrl($pennylaneInvoiceId);
    }

    /**
     * Polls the Pennylane changelog for customer invoices.
     * Pass $cursor for pagination within a batch, or $startDate for the initial request.
     * Do NOT pass both — Pennylane rejects requests with both parameters.
     * Returns ['items' => [...], 'has_more' => bool, 'next_cursor' => string|null].
     *
     * @throws RequestException
     */
    public function getInvoiceChanges(?string $cursor = null, ?string $startDate = null): array
    {
        $params = ['limit' => 100];
        if ($cursor) {
            $params['cursor'] = $cursor;
        } elseif ($startDate) {
            $params['start_date'] = $startDate;
        }

        return $this->http()
            ->get('/changelogs/customer_invoices', $params)
            ->throw()
            ->json();
    }

    /**
     * Returns true if the Pennylane invoice is fully paid (remaining_amount_with_tax == 0).
     *
     * @throws RequestException
     */
    public function isInvoicePaid(int|string $pennylaneInvoiceId): bool
    {
        $invoice = $this->http()
            ->get('/customer_invoices/' . $pennylaneInvoiceId)
            ->throw()
            ->json();

        return ($invoice['status'] ?? '') === 'paid' || ($invoice['paid'] ?? false) == true;
    }

    private function fetchPublicFileUrl(int|string $id): string
    {
        $invoice = $this->http()
            ->get('/customer_invoices/' . $id)
            ->throw()
            ->json();

        return $invoice['public_file_url'];
    }

    private function getEntreprise(Facture $facture): Entreprise
    {
        return $facture->reservations()->with('entreprise.adresseEntreprises')->first()->entreprise;
    }
}
