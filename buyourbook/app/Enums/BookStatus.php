<?php

namespace App\Enums;

enum BookStatus: string
{
    case Pending        = 'pending';
    case PickupPending  = 'pickup_pending';
    case Approved       = 'approved';
    case Rejected       = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending       => 'En attente',
            self::PickupPending => 'Collecte à domicile',
            self::Approved      => 'En ligne',
            self::Rejected      => 'Refusé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending       => 'yellow',
            self::PickupPending => 'blue',
            self::Approved      => 'green',
            self::Rejected      => 'red',
        };
    }
}
