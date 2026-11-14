<?php

namespace App\Enums;

enum PotentialCategory: string
{
    case PERTANIAN = 'pertanian';
    case PETERNAKAN = 'peternakan';
    case PERIKANAN = 'perikanan';
    case UMKM = 'umkm';
    case WISATA = 'wisata';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::PERTANIAN => 'Pertanian',
            self::PETERNAKAN => 'Peternakan',
            self::PERIKANAN => 'Perikanan',
            self::UMKM => 'UMKM & Industri Kreatif',
            self::WISATA => 'Pariwisata & Budaya',
            self::LAINNYA => 'Lainnya',
        };
    }
}

