<?php

namespace App\Services\Billing;

use App\Models\Reservation;
use App\Models\Facture;
use App\ValueObjects\FactureCalculation;

class FactureCalculService
{
    private const TVA_RATE = 0.10;

    public function calculateReservationTotal(Reservation $reservation): float
    {
        if ($reservation->tarif === null) {
            return 0.0;
        }

        $total = floatval($reservation->tarif);
        $montantMajoration = $total * (floatval($reservation->majoration) / 100);

        return $total + $montantMajoration + floatval($reservation->complement);
    }

    public function calculateFactureTotal(Facture $facture): float
    {
        $total = 0.0;

        foreach ($facture->reservations as $reservation) {
            $reservationTotal = $this->calculateReservationTotal($reservation);
            if ($reservationTotal > 0) {
                $total += $reservationTotal;
            }
        }

        return round($total, 2);
    }

    public function calculateTaxDetails(float $montantTTC): FactureCalculation
    {
        return FactureCalculation::fromTTC($montantTTC, self::TVA_RATE);
    }

    public function formatCurrency(float $amount): string
    {
        return number_format($amount, 2, ',', ' ') . ' €';
    }

    public function validateReservationForBilling(Reservation $reservation): bool
    {
        return $reservation->tarif !== null;
    }

    public function validateFactureForBilling(Facture $facture): array
    {
        $errors = [];

        foreach ($facture->reservations as $reservation) {
            if (!$this->validateReservationForBilling($reservation)) {
                $errors[] = "La réservation {$reservation->reference} n'a pas de tarif défini";
            }
        }

        return $errors;
    }
}