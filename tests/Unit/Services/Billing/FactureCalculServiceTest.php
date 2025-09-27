<?php

namespace Tests\Unit\Services\Billing;

use App\Models\Facture;
use App\Models\Reservation;
use App\Services\Billing\FactureCalculService;
use App\ValueObjects\FactureCalculation;
use Tests\TestCase;

class FactureCalculServiceTest extends TestCase
{
    private FactureCalculService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FactureCalculService();
    }

    public function testCalculateReservationTotalWithBasicTarif()
    {
        $reservation = new Reservation([
            'tarif' => 100.0,
            'majoration' => 0,
            'complement' => 0
        ]);

        $total = $this->service->calculateReservationTotal($reservation);

        $this->assertEquals(100.0, $total);
    }

    public function testCalculateReservationTotalWithMajoration()
    {
        $reservation = new Reservation([
            'tarif' => 100.0,
            'majoration' => 10, // 10%
            'complement' => 0
        ]);

        $total = $this->service->calculateReservationTotal($reservation);

        $this->assertEquals(110.0, $total);
    }

    public function testCalculateReservationTotalWithComplement()
    {
        $reservation = new Reservation([
            'tarif' => 100.0,
            'majoration' => 0,
            'complement' => 15.5
        ]);

        $total = $this->service->calculateReservationTotal($reservation);

        $this->assertEquals(115.5, $total);
    }

    public function testCalculateReservationTotalComplete()
    {
        $reservation = new Reservation([
            'tarif' => 100.0,
            'majoration' => 10, // 10%
            'complement' => 15.5
        ]);

        $total = $this->service->calculateReservationTotal($reservation);

        $this->assertEquals(125.5, $total); // 100 + 10 + 15.5
    }

    public function testCalculateReservationTotalWithNullTarif()
    {
        $reservation = new Reservation([
            'tarif' => null,
            'majoration' => 10,
            'complement' => 15.5
        ]);

        $total = $this->service->calculateReservationTotal($reservation);

        $this->assertEquals(0.0, $total);
    }

    public function testCalculateTaxDetails()
    {
        $calculation = $this->service->calculateTaxDetails(110.0);

        $this->assertInstanceOf(FactureCalculation::class, $calculation);
        $this->assertEquals(110.0, $calculation->montantTTC);
        $this->assertEquals(100.0, $calculation->montantHT);
        $this->assertEquals(10.0, $calculation->montantTVA);
        $this->assertEquals(0.10, $calculation->tauxTVA);
    }

    public function testFormatCurrency()
    {
        $formatted = $this->service->formatCurrency(1234.56);

        $this->assertEquals('1 234,56 €', $formatted);
    }

    public function testValidateReservationForBilling()
    {
        $validReservation = new Reservation(['tarif' => 100.0]);
        $invalidReservation = new Reservation(['tarif' => null]);

        $this->assertTrue($this->service->validateReservationForBilling($validReservation));
        $this->assertFalse($this->service->validateReservationForBilling($invalidReservation));
    }

    public function testCalculateFactureTotal()
    {
        $reservations = collect([
            new Reservation(['tarif' => 100.0, 'majoration' => 0, 'complement' => 0]),
            new Reservation(['tarif' => 200.0, 'majoration' => 10, 'complement' => 5]),
            new Reservation(['tarif' => null, 'majoration' => 0, 'complement' => 0])
        ]);

        $facture = $this->createMock(Facture::class);
        $facture->method('__get')->with('reservations')->willReturn($reservations);

        $total = $this->service->calculateFactureTotal($facture);

        $this->assertEquals(325.0, $total); // 100 + (200 + 20 + 5) + 0
    }

    public function testValidateFactureForBilling()
    {
        $reservations = collect([
            new Reservation(['tarif' => 100.0]),
            new Reservation(['tarif' => null]),
            new Reservation(['tarif' => 200.0])
        ]);

        $facture = $this->createMock(Facture::class);
        $facture->method('__get')->with('reservations')->willReturn($reservations);

        $errors = $this->service->validateFactureForBilling($facture);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('tarif', $errors[0]);
    }
}