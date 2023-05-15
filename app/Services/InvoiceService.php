<?php

namespace App\Services;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
use app\Settings\BillSettings;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

        $physique = new Party([
            'name' => $entreprise->nom,
            'address' => $facture->adresse_client
        ]);

        $facturation = new Party([
            'name' => $entreprise->nom,
            'address' => $facture->adresse_facturation
        ]);

        $invoiceDate = Carbon::create($facture->year, $facture->month, 1, 0, 0, 0, 'Europe/Paris');

        $invoice = Invoice::make($facture->reference)
            ->seller($physique)
            ->buyer($facturation)
            ->currencySymbol('€')
            ->currencyCode('EUR')
            ->currencyThousandsSeparator(' ')
            ->currencyDecimalPoint(',')
            ->addItem(
                (new InvoiceItem())
                    ->title('Transports pour la période :')
                    ->description($invoiceDate->monthName . ' ' . $invoiceDate->year)
                    ->pricePerUnit($facture->montant_ht)
            )
            ->taxRate(10)
            ->logo(public_path('storage/motobleu-dark.png'))
        ;

        ray($facture->information);

        if ($notes = $facture->information) {

            $invoice->notes($notes);
        }

        if ($facture->is_acquitte) {
            $invoice->status('ACQUITTÉE');
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

    /**
     * @param Entreprise $entreprise
     * @param AdresseEntrepriseTypeEnum $adresseEntrepriseType
     * @return AdresseEntreprise|null
     */
    private function getAdresse(Entreprise $entreprise, AdresseEntrepriseTypeEnum $adresseEntrepriseType): ?AdresseEntreprise
    {
        return AdresseEntreprise::where('entreprise_id', $entreprise->id)
            ->where('type', $adresseEntrepriseType)
            ->first();
    }
}
