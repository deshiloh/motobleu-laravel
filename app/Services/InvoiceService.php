<?php

namespace App\Services;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
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

        $addressPhysique = (new self())->getAdresse($entreprise, AdresseEntrepriseTypeEnum::PHYSIQUE);
        $addressFacturation = (new self())->getAdresse($entreprise, AdresseEntrepriseTypeEnum::FACTURATION);

        $physique = new Party([
            'name' => $entreprise->nom,
            'address' => $addressPhysique->adresse_full
        ]);

        $facturation = new Party([
            'name' => $entreprise->nom,
            'address' => $addressFacturation->adresse_full
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
                    ->title('Transports pour la période de ' . $invoiceDate->monthName . ' ' . $invoiceDate->year)
                    ->description($facture->information ?? '')
                    ->pricePerUnit($facture->montant_ht)
            )
            ->taxRate(10)
            //->logo(public_path('storage/logo-pdf.png'))
        ;

        if ($facture->is_acquitte) {
            $invoice->status('ACQUITTE');
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
