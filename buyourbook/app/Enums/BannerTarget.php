<?php

namespace App\Enums;

enum BannerTarget: string
{
    case All = 'all';
    case School = 'school';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Tous',
            self::School => 'École spécifique',
        };
    }
}
