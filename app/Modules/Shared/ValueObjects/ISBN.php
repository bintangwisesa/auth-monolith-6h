<?php

namespace Modules\Shared\ValueObjects;

use InvalidArgumentException;

class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        $clean = preg_replace('/[\s-]/', '', $value);

        if (!preg_match('/^\d{13}$/', $clean)) {
            throw new InvalidArgumentException("ISBN harus 13 digit: {$value}");
        }

        if ((int) $clean[12] !== self::checkDigit(substr($clean, 0, 12))) {
            throw new InvalidArgumentException("Checksum ISBN tidak valid: {$value}");
        }

        $this->value = $clean;
    }

    public static function checkDigit(string $first12): int
    {
        $sum = 0;
        foreach (str_split($first12) as $i => $digit) {
            $sum += (int) $digit * ($i % 2 === 0 ? 1 : 3);
        }
        return (10 - ($sum % 10)) % 10;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function formatted(): string
    {
        return substr($this->value, 0, 3) . '-' . substr($this->value, 3, 1) . '-'
            . substr($this->value, 4, 6) . '-' . substr($this->value, 10, 2) . '-'
            . substr($this->value, 12, 1);
    }

    public function equals(Isbn $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}