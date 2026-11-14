<?php

namespace Database\Seeders;

use App\Models\VillageProfile;
use Illuminate\Database\Seeder;

class VillageProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VillageProfile::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Gelam Jaya',
                'village_code' => '36.03.11.2005',
                'address' => 'Jl. Villa Regency Tangerang II No. 01 Desa Gelam Jaya',
                'phone' => '(021) 5900000',
                'email' => 'kantor@gelamjaya.desa.id',
                'district' => 'Pasar Kemis',
                'regency' => 'Kabupaten Tangerang',
                'province' => 'Banten',
                'postal_code' => '15560',
                'logo_path' => null,
                'area_size_ha' => 294.00,
                'total_population' => 40030,
                'total_households' => 11015,
                'male_population' => 20218,
                'female_population' => 19812,
                'boundary_north' => 'Kel. Kutabumi',
                'boundary_east' => 'Kel. Priuk',
                'boundary_south' => 'Kel. Alamjaya',
                'boundary_west' => 'Kel. Kutajaya',
                'vision' => 'Terwujudnya Desa Gelam Jaya yang Maju, Mandiri, Sejahtera, dan Berakhlak Mulia berbasis Pelayanan Administrasi Digital Terpadu.',
                'mission' => "1. Meningkatkan kualitas pelayanan publik berbasis teknologi informasi yang cepat, transparan, dan akuntabel.\n2. Mewujudkan tata kelola pemerintahan desa yang bersih, berintegritas, dan partisipatif.\n3. Mengembangkan potensi ekonomi desa melalui kemitraan UMKM, pertanian modern, dan perdagangan lokal.\n4. Meningkatkan sarana dan prasarana umum, pendidikan, dan kesehatan masyarakat desa yang inklusif.",
            ]
        );
    }
}
