<?php

namespace App\Services\Billing;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Enum\ReservationStatus;
use App\Models\AdresseEntreprise;
use App\Models\Facture;
use App\Models\Reservation;
use App\ValueObjects\BillingPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FactureGenerationService
{
    public function getUnbilledReservations(int $entrepriseId, BillingPeriod $period): Collection
    {
        return Reservation::where('entreprise_id', $entrepriseId)
            ->whereMonth('pickup_date', $period->month)
            ->whereYear('pickup_date', $period->year)
            ->whereIn('statut', [
                ReservationStatus::Confirmed->value,
                ReservationStatus::CanceledToPay->value
            ])
            ->where(function (Builder $query) {
                $query
                    ->whereNull('encaisse_pilote')
                    ->orWhere('encaisse_pilote', 0);
            })
            ->get();
    }

    public function generateFacture(int $entrepriseId, BillingPeriod $period): Facture
    {
        $addresses = $this->getBillingAddresses($entrepriseId);
        $reference = Facture::generateReference($period->year, $period->month);

        return Facture::create([
            'reference' => $reference,
            'month' => $period->month,
            'year' => $period->year,
            'adresse_client' => $addresses['client'],
            'adresse_facturation' => $addresses['facturation']
        ]);
    }

    public function attachReservationsToFacture(Collection $reservations, Facture $facture): void
    {
        foreach ($reservations as $reservation) {
            if ($reservation->facture_id === null) {
                $reservation->updateQuietly([
                    'facture_id' => $facture->id
                ]);
            }
        }
    }

    private function getBillingAddresses(int $entrepriseId): array
    {
        $addressBillEntreprise = AdresseEntreprise::where('type', AdresseEntrepriseTypeEnum::FACTURATION)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        $addressLocalEntreprise = AdresseEntreprise::where('type', AdresseEntrepriseTypeEnum::PHYSIQUE)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        $addressFacturation = $addressBillEntreprise?->address_bill_format
            ?? $addressLocalEntreprise?->address_bill_format;

        $addressLocalFacturation = $addressLocalEntreprise?->address_bill_format
            ?? $addressBillEntreprise?->address_bill_format;

        return [
            'facturation' => $addressFacturation,
            'client' => $addressLocalFacturation
        ];
    }
}