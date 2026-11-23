<?php

namespace App\Enums;

enum RequestStatus: string
{
    case DIAJUKAN = 'diajukan';
    case PERLU_PERBAIKAN = 'perlu_perbaikan';
    case DIVERIFIKASI = 'diverifikasi';
    case DIPROSES = 'diproses';
    case SIAP_DIAMBIL = 'siap_diambil';
    case SELESAI = 'selesai';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::PERLU_PERBAIKAN => 'Perlu Perbaikan',
            self::DIVERIFIKASI => 'Diverifikasi',
            self::DIPROSES => 'Diproses',
            self::SIAP_DIAMBIL => 'Siap Diambil',
            self::SELESAI => 'Selesai',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'blue',
            self::PERLU_PERBAIKAN => 'orange',
            self::DIVERIFIKASI => 'indigo',
            self::DIPROSES => 'yellow',
            self::SIAP_DIAMBIL => 'teal',
            self::SELESAI => 'emerald',
            self::DITOLAK => 'rose',
        };
    }

    /**
     * Determine if status is currently active (not in terminal state).
     */
    public function isActive(): bool
    {
        return !in_array($this, [self::SELESAI, self::DITOLAK], true);
    }
}
