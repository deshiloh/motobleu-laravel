<?php

namespace App\ValueObjects;

use Carbon\Carbon;

readonly class BillingPeriod
{
    public function __construct(
        public int $month,
        public int $year
    ) {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Month must be between 1 and 12');
        }

        if ($year < 1900 || $year > 2100) {
            throw new \InvalidArgumentException('Year must be between 1900 and 2100');
        }
    }

    public static function current(): self
    {
        $now = Carbon::now();
        return new self($now->month, $now->year);
    }

    public static function fromDate(Carbon $date): self
    {
        return new self($date->month, $date->year);
    }

    public function getMonthName(): string
    {
        return match ($this->month) {
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
        };
    }

    public function getStartDate(): Carbon
    {
        return Carbon::create($this->year, $this->month, 1)->startOfMonth();
    }

    public function getEndDate(): Carbon
    {
        return Carbon::create($this->year, $this->month, 1)->endOfMonth();
    }

    public function format(string $separator = '/'): string
    {
        return sprintf("%02d{$separator}%d", $this->month, $this->year);
    }

    public function equals(BillingPeriod $other): bool
    {
        return $this->month === $other->month && $this->year === $other->year;
    }

    public function isAfter(BillingPeriod $other): bool
    {
        return $this->year > $other->year ||
               ($this->year === $other->year && $this->month > $other->month);
    }

    public function isBefore(BillingPeriod $other): bool
    {
        return $this->year < $other->year ||
               ($this->year === $other->year && $this->month < $other->month);
    }

    public function next(): self
    {
        if ($this->month === 12) {
            return new self(1, $this->year + 1);
        }

        return new self($this->month + 1, $this->year);
    }

    public function previous(): self
    {
        if ($this->month === 1) {
            return new self(12, $this->year - 1);
        }

        return new self($this->month - 1, $this->year);
    }

    public function toArray(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
            'month_name' => $this->getMonthName(),
            'formatted' => $this->format(),
        ];
    }
}