<?php

namespace App\Enums;

enum BannerPosition: string
{
    case HomeTop = 'home_top';
    case HomeMid = 'home_mid';
    case Sidebar = 'sidebar';

    public function label(): string
    {
        return match ($this) {
            self::HomeTop => 'Accueil - Haut',
            self::HomeMid => 'Accueil - Milieu',
            self::Sidebar => 'Barre latérale',
        };
    }
}
