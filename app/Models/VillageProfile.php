<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillageProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'village_code',
        'address',
        'phone',
        'email',
        'district',
        'regency',
        'province',
        'postal_code',
        'logo_path',
        'area_size_ha',
        'total_population',
        'total_households',
        'male_population',
        'female_population',
        'boundary_north',
        'boundary_south',
        'boundary_east',
        'boundary_west',
        'vision',
        'mission',
    ];

    protected function casts(): array
    {
        return [
            'area_size_ha' => 'decimal:2',
            'total_population' => 'integer',
            'total_households' => 'integer',
            'male_population' => 'integer',
            'female_population' => 'integer',
        ];
    }

    /**
     * Get the singleton village profile record.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'name' => 'Gelam Jaya',
            'district' => 'Pasar Kemis',
            'regency' => 'Kabupaten Tangerang',
            'province' => 'Banten',
            'postal_code' => '15560',
            'address' => 'Jl. Villa Regency Tangerang II No. 01 Desa Gelam Jaya',
            'phone' => '(021) 5900000',
            'email' => 'kantor@gelamjaya.desa.id',
            'village_code' => '36.03.11.2005',
            'area_size_ha' => 294.00,
            'total_population' => 40030,
            'total_households' => 11015,
            'male_population' => 20218,
            'female_population' => 19812,
            'boundary_north' => 'Kel. Kutabumi',
            'boundary_south' => 'Kel. Alamjaya',
            'boundary_east' => 'Kel. Priuk',
            'boundary_west' => 'Kel. Kutajaya',
            'vision' => 'Terwujudnya Desa Gelam Jaya yang Maju, Mandiri, Sejahtera, dan Berakhlak Mulia berbasis Pelayanan Digital Terpadu.',
            'mission' => "1. Meningkatkan kualitas pelayanan publik berbasis teknologi informasi yang cepat, transparan, dan akuntabel.\n2. Mewujudkan tata kelola pemerintahan desa yang bersih, transparan, dan partisipatif.\n3. Mengembangkan potensi ekonomi desa melalui pemberdayaan UMKM, pertanian, dan perdagangan lokal.\n4. Meningkatkan sarana dan prasarana umum, pendidikan, dan kesehatan masyarakat desa.",
        ]);
    }
}

