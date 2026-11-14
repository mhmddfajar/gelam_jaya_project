<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillagePotential extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'pertanian' => 'Pertanian',
        'peternakan' => 'Peternakan',
        'perikanan' => 'Perikanan',
        'umkm' => 'UMKM & Perdagangan',
        'wisata' => 'Wisata & Budaya',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'title',
        'category',
        'description',
        'location',
        'photo_path',
    ];
}

