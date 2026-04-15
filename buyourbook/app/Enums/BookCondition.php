<?php

namespace App\Enums;

enum BookCondition: string
{
    case New = 'new';
    case Good = 'good';
    case Acceptable = 'acceptable';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Neuf',
            self::Good => 'Bon état',
            self::Acceptable => 'Acceptable',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'green',
            self::Good => 'blue',
            self::Acceptable => 'yellow',
        };
    }
}
