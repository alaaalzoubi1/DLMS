<?php

namespace App\ValueObjects;

final class Money
{
    private int $amount; // بالهللات

    private function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public static function fromMajor(string $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public static function fromMinor(int $amount): self
    {
        return new self($amount);
    }

    public function toMinor(): int
    {
        return $this->amount;
    }

    public function toMajor(): string
    {
        return number_format($this->amount / 100, 2, '.', '');
    }

    public function multiply(int $quantity): self
    {
        return new self($this->amount * $quantity);
    }

    public function percentage(int $basisPoints): self
    {
        return new self(intdiv($this->amount * $basisPoints, 10000));
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }
}
