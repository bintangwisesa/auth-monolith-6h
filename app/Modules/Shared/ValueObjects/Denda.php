<?php

namespace Modules\Shared\ValueObjects;

use Carbon\CarbonInterface;
use InvalidArgumentException;

class Denda
{
    public const RATE_PER_DAY = 1000; // Rp1.000 per buku per hari

    private function __construct(
        private int $daysLate,
        private float $amount
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Denda tidak boleh negatif');
        }
    }

    public static function calculate(
        CarbonInterface $dueDate,
        CarbonInterface $returnedAt,
        int $quantity = 1,
        int $ratePerDay = self::RATE_PER_DAY
    ): self {
        $due = $dueDate->copy()->startOfDay();
        $ret = $returnedAt->copy()->startOfDay();

        $days = $ret->greaterThan($due) ? (int) $due->diffInDays($ret) : 0;

        return new self($days, $days * $ratePerDay * $quantity);
    }

    public static function zero(): self
    {
        return new self(0, 0);
    }

    public function add(Denda $other): self
    {
        return new self($this->daysLate + $other->daysLate, $this->amount + $other->amount);
    }

    public function getDaysLate(): int { return $this->daysLate; }
    public function getAmount(): float { return $this->amount; }
    public function isZero(): bool { return $this->amount === 0.0; }

    public function __toString(): string
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }
}