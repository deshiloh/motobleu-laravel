<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\FactureCalculation;
use Tests\TestCase;

class FactureCalculationTest extends TestCase
{
    public function testCreateFromTTC()
    {
        $calculation = FactureCalculation::fromTTC(110.0, 0.10);

        $this->assertEquals(100.0, $calculation->montantHT);
        $this->assertEquals(10.0, $calculation->montantTVA);
        $this->assertEquals(110.0, $calculation->montantTTC);
        $this->assertEquals(0.10, $calculation->tauxTVA);
    }

    public function testCreateFromHT()
    {
        $calculation = FactureCalculation::fromHT(100.0, 0.10);

        $this->assertEquals(100.0, $calculation->montantHT);
        $this->assertEquals(10.0, $calculation->montantTVA);
        $this->assertEquals(110.0, $calculation->montantTTC);
        $this->assertEquals(0.10, $calculation->tauxTVA);
    }

    public function testCreateFromTTCWithDefaultTVA()
    {
        $calculation = FactureCalculation::fromTTC(110.0);

        $this->assertEquals(100.0, $calculation->montantHT);
        $this->assertEquals(10.0, $calculation->montantTVA);
        $this->assertEquals(110.0, $calculation->montantTTC);
        $this->assertEquals(0.10, $calculation->tauxTVA);
    }

    public function testFormatMontantHT()
    {
        $calculation = FactureCalculation::fromTTC(1234.56);

        $this->assertEquals('1 122,33 €', $calculation->formatMontantHT());
    }

    public function testFormatMontantTVA()
    {
        $calculation = FactureCalculation::fromTTC(1234.56);

        $this->assertEquals('112,23 €', $calculation->formatMontantTVA());
    }

    public function testFormatMontantTTC()
    {
        $calculation = FactureCalculation::fromTTC(1234.56);

        $this->assertEquals('1 234,56 €', $calculation->formatMontantTTC());
    }

    public function testFormatTauxTVA()
    {
        $calculation = FactureCalculation::fromTTC(110.0, 0.15);

        $this->assertEquals('15.0%', $calculation->formatTauxTVA());
    }

    public function testIsZero()
    {
        $zeroCalculation = FactureCalculation::fromTTC(0.0);
        $nonZeroCalculation = FactureCalculation::fromTTC(110.0);

        $this->assertTrue($zeroCalculation->isZero());
        $this->assertFalse($nonZeroCalculation->isZero());
    }

    public function testIsPositive()
    {
        $zeroCalculation = FactureCalculation::fromTTC(0.0);
        $positiveCalculation = FactureCalculation::fromTTC(110.0);

        $this->assertFalse($zeroCalculation->isPositive());
        $this->assertTrue($positiveCalculation->isPositive());
    }

    public function testToArray()
    {
        $calculation = FactureCalculation::fromTTC(110.0, 0.10);
        $array = $calculation->toArray();

        $expected = [
            'montant_ht' => 100.0,
            'montant_tva' => 10.0,
            'montant_ttc' => 110.0,
            'taux_tva' => 0.10,
            'formatted' => [
                'montant_ht' => '100,00 €',
                'montant_tva' => '10,00 €',
                'montant_ttc' => '110,00 €',
                'taux_tva' => '10.0%',
            ]
        ];

        $this->assertEquals($expected, $array);
    }

    public function testRounding()
    {
        $calculation = FactureCalculation::fromTTC(123.456789);

        $this->assertEquals(112.24, $calculation->montantHT);
        $this->assertEquals(11.22, $calculation->montantTVA);
        $this->assertEquals(123.46, $calculation->montantTTC);
    }
}