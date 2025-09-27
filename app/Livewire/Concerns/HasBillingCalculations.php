<?php

namespace App\Livewire\Concerns;

use App\Services\Billing\FactureCalculService;
use App\ValueObjects\FactureCalculation;
use App\Models\Reservation;
use App\Models\Facture;

trait HasBillingCalculations
{
    protected ?FactureCalculService $calculService = null;

    public function bootHasBillingCalculations(): void
    {
        $this->calculService = app(FactureCalculService::class);
    }

    protected function getCalculService(): FactureCalculService
    {
        if ($this->calculService === null) {
            $this->calculService = app(FactureCalculService::class);
        }
        return $this->calculService;
    }

    protected function calculateReservationTotal(Reservation $reservation): float
    {
        return $this->getCalculService()->calculateReservationTotal($reservation);
    }

    protected function calculateFactureTotal(Facture $facture): float
    {
        return $this->getCalculService()->calculateFactureTotal($facture);
    }

    protected function getTaxDetails(float $montantTTC): FactureCalculation
    {
        return FactureCalculation::fromTTC($montantTTC);
    }

    protected function formatCurrency(float $amount): string
    {
        return $this->getCalculService()->formatCurrency($amount);
    }

    protected function validateFactureForBilling(Facture $facture): array
    {
        return $this->getCalculService()->validateFactureForBilling($facture);
    }
}