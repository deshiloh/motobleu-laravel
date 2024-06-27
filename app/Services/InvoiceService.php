<?php

namespace App\Services;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Enum\BillStatut;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
use app\Settings\BillSettings;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class InvoiceService
{
    /**
     * @throws BindingResolutionException
     * @throws \Exception
     */
    public static function generateInvoice(Facture $facture): Invoice
    {
        $entreprise = (new self())->getEntreprise($facture);
        $months = [];

        $physique = new Party([
            'name' => $entreprise->nom,
            'address' => $facture->adresse_client
        ]);

        $facturation = new Party([
            'name' => $entreprise->nom,
            'address' => $facture->adresse_facturation
        ]);

        $invoiceDate = Carbon::create($facture->year, $facture->month, 1, 0, 0, 0, 'Europe/Paris');
        $reservations = $facture->reservations()
            ->orderBy('pickup_date')
            ->get();

        foreach ($reservations as $reservation) {
            $months[] = $reservation->pickup_date->monthName;
        }

        $months = array_unique($months);

        $invoice = Invoice::make($facture->reference)
            ->date($facture->created_at)
            ->dateFormat('d/m/Y')
            ->seller($physique)
            ->buyer($facturation)
            ->currencySymbol('€')
            ->currencyCode('EUR')
            ->currencyThousandsSeparator(' ')
            ->currencyDecimalPoint(',')
            ->addItem(
                (new InvoiceItem())
                    ->title('Transports pour la période :')
                    ->description(implode(', ', $months) . ' ' . $invoiceDate->year)
                    ->pricePerUnit($facture->montant_ht)
            )
            ->taxRate(10)
            ->logo(public_path('storage/motobleu-dark.png'))
        ;

        $invoice->setCustomData([
            'montant_ttc' => $facture->montant_ttc
        ]);

        if ($notes = $facture->information) {
            $invoice->notes($notes);
        }

        if ($facture->is_acquitte) {
            $invoice->status('ACQUITTÉE');
        }

        if ($facture->statut === BillStatut::CANCEL) {
            $invoice->status('ANNULÉE');
        }

        $invoice->template('motobleu');
        $invoice->table_columns = 3;

        return $invoice;
    }

    private function getEntreprise(Facture $facture): Model|null
    {
        return Entreprise::query()
            ->select('entreprises.*')
            ->join('reservations', 'reservations.entreprise_id', '=', 'entreprises.id')
            ->join('factures', 'reservations.facture_id', '=', 'factures.id')
            ->where('factures.id', $facture->id)
            ->first();
    }

    private function getBilledPeriod(Facture $facture): array
    {
        $months = [];
        $reservations = $facture->reservations()
            ->orderBy('pickup_date')
            ->get();

        foreach ($reservations as $reservation) {
            $months[] = $reservation->pickup_date->monthName;
        }

        return array_unique($months);
    }
}
