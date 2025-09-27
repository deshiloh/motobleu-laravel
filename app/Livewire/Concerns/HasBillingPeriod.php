<?php

namespace App\Livewire\Concerns;

use App\ValueObjects\BillingPeriod;

trait HasBillingPeriod
{
    public ?int $selectedMonth = null;
    public ?int $selectedYear = null;

    public function mountHasBillingPeriod(): void
    {
        $currentPeriod = BillingPeriod::current();

        $this->selectedMonth = $this->selectedMonth ?? $currentPeriod->month;
        $this->selectedYear = $this->selectedYear ?? $currentPeriod->year;
    }

    protected function getBillingPeriod(): BillingPeriod
    {
        return new BillingPeriod($this->selectedMonth, $this->selectedYear);
    }

    protected function getMonthsOptions(): array
    {
        return [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];
    }

    protected function getYearsRange(int $yearsBack = 4, int $yearsForward = 4): array
    {
        $currentYear = now()->year;
        $startYear = $currentYear - $yearsBack;
        $endYear = $currentYear + $yearsForward;

        $years = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $years[$year] = $year;
        }

        return $years;
    }
}