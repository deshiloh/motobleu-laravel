<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\BillingPeriod;
use Carbon\Carbon;
use Tests\TestCase;

class BillingPeriodTest extends TestCase
{
    public function testCreateBillingPeriod()
    {
        $period = new BillingPeriod(3, 2024);

        $this->assertEquals(3, $period->month);
        $this->assertEquals(2024, $period->year);
    }

    public function testInvalidMonth()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Month must be between 1 and 12');

        new BillingPeriod(13, 2024);
    }

    public function testInvalidYear()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Year must be between 1900 and 2100');

        new BillingPeriod(3, 1800);
    }

    public function testCreateFromCurrent()
    {
        Carbon::setTestNow(Carbon::create(2024, 6, 15));

        $period = BillingPeriod::current();

        $this->assertEquals(6, $period->month);
        $this->assertEquals(2024, $period->year);
    }

    public function testCreateFromDate()
    {
        $date = Carbon::create(2024, 9, 15);
        $period = BillingPeriod::fromDate($date);

        $this->assertEquals(9, $period->month);
        $this->assertEquals(2024, $period->year);
    }

    public function testGetMonthName()
    {
        $period = new BillingPeriod(3, 2024);

        $this->assertEquals('Mars', $period->getMonthName());
    }

    public function testGetStartDate()
    {
        $period = new BillingPeriod(3, 2024);
        $startDate = $period->getStartDate();

        $this->assertEquals('2024-03-01 00:00:00', $startDate->format('Y-m-d H:i:s'));
    }

    public function testGetEndDate()
    {
        $period = new BillingPeriod(3, 2024);
        $endDate = $period->getEndDate();

        $this->assertEquals('2024-03-31 23:59:59', $endDate->format('Y-m-d H:i:s'));
    }

    public function testFormat()
    {
        $period = new BillingPeriod(3, 2024);

        $this->assertEquals('03/2024', $period->format());
        $this->assertEquals('03-2024', $period->format('-'));
    }

    public function testEquals()
    {
        $period1 = new BillingPeriod(3, 2024);
        $period2 = new BillingPeriod(3, 2024);
        $period3 = new BillingPeriod(4, 2024);

        $this->assertTrue($period1->equals($period2));
        $this->assertFalse($period1->equals($period3));
    }

    public function testIsAfter()
    {
        $period1 = new BillingPeriod(3, 2024);
        $period2 = new BillingPeriod(2, 2024);
        $period3 = new BillingPeriod(3, 2023);

        $this->assertTrue($period1->isAfter($period2));
        $this->assertTrue($period1->isAfter($period3));
        $this->assertFalse($period2->isAfter($period1));
    }

    public function testIsBefore()
    {
        $period1 = new BillingPeriod(2, 2024);
        $period2 = new BillingPeriod(3, 2024);
        $period3 = new BillingPeriod(2, 2025);

        $this->assertTrue($period1->isBefore($period2));
        $this->assertTrue($period1->isBefore($period3));
        $this->assertFalse($period2->isBefore($period1));
    }

    public function testNext()
    {
        $period = new BillingPeriod(3, 2024);
        $next = $period->next();

        $this->assertEquals(4, $next->month);
        $this->assertEquals(2024, $next->year);

        // Test year rollover
        $december = new BillingPeriod(12, 2024);
        $nextYear = $december->next();

        $this->assertEquals(1, $nextYear->month);
        $this->assertEquals(2025, $nextYear->year);
    }

    public function testPrevious()
    {
        $period = new BillingPeriod(3, 2024);
        $previous = $period->previous();

        $this->assertEquals(2, $previous->month);
        $this->assertEquals(2024, $previous->year);

        // Test year rollover
        $january = new BillingPeriod(1, 2024);
        $previousYear = $january->previous();

        $this->assertEquals(12, $previousYear->month);
        $this->assertEquals(2023, $previousYear->year);
    }

    public function testToArray()
    {
        $period = new BillingPeriod(3, 2024);
        $array = $period->toArray();

        $expected = [
            'month' => 3,
            'year' => 2024,
            'month_name' => 'Mars',
            'formatted' => '03/2024',
        ];

        $this->assertEquals($expected, $array);
    }
}