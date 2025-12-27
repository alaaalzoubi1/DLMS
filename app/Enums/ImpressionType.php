<?php

namespace App\Enums;

enum ImpressionType: int
{
    case DIGITAL = 1;
    case TRADITIONAL = 2;
    case BOTH = 3;

    public function label(): string
    {
        return match ($this) {
            self::DIGITAL => 'الكترونية',
            self::TRADITIONAL => 'تقليدية',
            self::BOTH => 'الكترونية + تقليدية',
        };
    }
}
