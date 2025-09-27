<?php

namespace App\ValueObjects;

readonly class FactureCalculation
{
    public function __construct(
        public float $montantHT,
        public float $montantTVA,
        public float $montantTTC,
        public float $tauxTVA
    ) {}

    public static function fromTTC(float $montantTTC, float $tauxTVA = 0.10): self
    {
        $montantTTC = round($montantTTC, 2);
        $montantHT = $montantTTC / (1 + $tauxTVA);
        $montantTVA = $montantTTC - $montantHT;

        return new self(
            round($montantHT, 2),
            round($montantTVA, 2),
            $montantTTC,
            $tauxTVA
        );
    }

    public static function fromHT(float $montantHT, float $tauxTVA = 0.10): self
    {
        $montantHT = round($montantHT, 2);
        $montantTVA = $montantHT * $tauxTVA;
        $montantTTC = $montantHT + $montantTVA;

        return new self(
            $montantHT,
            round($montantTVA, 2),
            round($montantTTC, 2),
            $tauxTVA
        );
    }

    public function formatMontantHT(): string
    {
        return number_format($this->montantHT, 2, ',', ' ') . ' €';
    }

    public function formatMontantTVA(): string
    {
        return number_format($this->montantTVA, 2, ',', ' ') . ' €';
    }

    public function formatMontantTTC(): string
    {
        return number_format($this->montantTTC, 2, ',', ' ') . ' €';
    }

    public function formatTauxTVA(): string
    {
        return number_format($this->tauxTVA * 100, 1) . '%';
    }

    public function isZero(): bool
    {
        return $this->montantTTC === 0.0;
    }

    public function isPositive(): bool
    {
        return $this->montantTTC > 0.0;
    }

    public function toArray(): array
    {
        return [
            'montant_ht' => $this->montantHT,
            'montant_tva' => $this->montantTVA,
            'montant_ttc' => $this->montantTTC,
            'taux_tva' => $this->tauxTVA,
            'formatted' => [
                'montant_ht' => $this->formatMontantHT(),
                'montant_tva' => $this->formatMontantTVA(),
                'montant_ttc' => $this->formatMontantTTC(),
                'taux_tva' => $this->formatTauxTVA(),
            ]
        ];
    }
}