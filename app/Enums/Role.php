<?php

namespace App\Enums;

enum Role: string
{
    case WARGA = 'warga';
    case STAF = 'staf';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::WARGA => 'Warga',
            self::STAF => 'Staf',
            self::ADMIN => 'Admin',
        };
    }
}

