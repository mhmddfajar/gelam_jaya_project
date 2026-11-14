<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'pendidikan' => 'Pendidikan',
        'kesehatan' => 'Kesehatan',
        'ibadah' => 'Sarana Ibadah',
        'olahraga' => 'Olahraga & Rekreasi',
        'jalan_transportasi' => 'Jalan & Transportasi',
        'fasilitas_umum' => 'Fasilitas Umum & Sosial',
    ];

    public const CONDITIONS = [
        'baik' => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat' => 'Rusak Berat',
    ];

    protected $fillable = [
        'name',
        'category',
        'location',
        'quantity',
        'condition',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }
}

