<?php

namespace Database\Seeders;

use App\Models\VillagePotential;
use Illuminate\Database\Seeder;

class VillagePotentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VillagePotential::query()->delete();

        $potentials = [
            [
                'title' => 'Sentra Kerajinan Anyaman & Olahan Kreatif UMKM',
                'category' => 'umkm',
                'location' => 'Dusun I, Desa Gelam Jaya',
                'description' => 'Produksi kerajinan rumah tangga dan produk olahan makanan khas warga Gelam Jaya yang telah menembus pasar regional Tangerang dan Jabodetabek.',
            ],
            [
                'title' => 'Kawasan Budidaya Ikan Nila & Lele Bioflok Mandiri',
                'category' => 'perikanan',
                'location' => 'Dusun II, Desa Gelam Jaya',
                'description' => 'Pemanfaatan sistem bioflok modern ramah lingkungan dengan produktivitas tinggi, menyuplai kebutuhan pangan segar warga dan restoran sekitar.',
            ],
            [
                'title' => 'Pertanian Hidroponik & Sayuran Hijau Urban Farming',
                'category' => 'pertanian',
                'location' => 'Dusun III, Desa Gelam Jaya',
                'description' => 'Pengembangan komoditas sayuran hijau (selada, pakcoy, kangkung) dengan metode hidroponik ramah lingkungan yang dikelola kelompok tani muda desa.',
            ],
            [
                'title' => 'Taman Hijau & Kawasan Terbuka Ramah Anak',
                'category' => 'wisata',
                'location' => 'Dusun IV, Desa Gelam Jaya',
                'description' => 'Area terbuka hijau publik yang asri untuk rekreasi warga, ruang interaksi sosial komunitas, dan sarana edukasi lingkungan hidup keluarga.',
            ],
            [
                'title' => 'Peternakan Rakyat & Sentra Unggas Produktif',
                'category' => 'peternakan',
                'location' => 'Dusun I, Desa Gelam Jaya',
                'description' => 'Peternakan rakyat mandiri dengan tata kelola higienis yang menghasilkan pasokan telur, susu segar, dan kebutuhan hewani pasar lokal.',
            ],
        ];

        foreach ($potentials as $data) {
            VillagePotential::create($data);
        }
    }
}
