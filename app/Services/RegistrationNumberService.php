<?php

namespace App\Services;

use App\Models\LetterRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegistrationNumberService
{
    /**
     * Generate unique registration number with format REG-YYYYMMDD-NNNN.
     */
    public function generate(?Carbon $date = null): string
    {
        $date = $date ?: Carbon::now();
        $dateStr = $date->format('Ymd');
        $prefix = "REG-{$dateStr}-";

        // Query the latest registration number for the given date
        $latest = LetterRequest::where('registration_no', 'like', "{$prefix}%")
            ->orderBy('registration_no', 'desc')
            ->value('registration_no');

        if ($latest) {
            $lastNumber = (int) substr($latest, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $paddedNumber = str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$paddedNumber}";
    }
}
