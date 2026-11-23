<?php

namespace App\Enums;

enum Channel: string
{
    case DARING = 'daring';
    case LURING = 'luring';

    public function label(): string
    {
        return match ($this) {
            self::DARING => 'Daring (Online)',
            self::LURING => 'Luring (Loket Kantor)',
        };
    }
}
