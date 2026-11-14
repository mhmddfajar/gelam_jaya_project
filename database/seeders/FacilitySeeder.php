<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Facility::query()->delete();

        // 1. Pendidikan
        $schools = [
            [
                'name' => 'SDN 1 Gelam Jaya',
                'category' => 'pendidikan',
                'location' => 'Dusun I, Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Gedung Sekolah Dasar Negeri 1 Gelam Jaya dengan fasilitas ruang belajar dan kegiatan siswa.',
            ],
            [
                'name' => 'SDN 2 Gelam Jaya',
                'category' => 'pendidikan',
                'location' => 'Dusun II, Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Gedung Sekolah Dasar Negeri 2 Gelam Jaya dengan sarana pembelajaran memadai.',
            ],
            [
                'name' => 'SMP N 3 Pasar Kemis',
                'category' => 'pendidikan',
                'location' => 'Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Sekolah Menengah Pertama Negeri 3 Pasar Kemis yang berlokasi di wilayah Desa Gelam Jaya.',
            ],
            [
                'name' => 'SMP Islam Bunaya',
                'category' => 'pendidikan',
                'location' => 'Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Sekolah Menengah Pertama Islam terpadu Bunaya.',
            ],
            [
                'name' => 'SMP/SMA Tarsisius',
                'category' => 'pendidikan',
                'location' => 'Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Kompleks persekolahan swasta Tarsisius (jenjang SMP & SMA) di kawasan Gelam Jaya.',
            ],
            [
                'name' => 'SMP/SMA Penabur',
                'category' => 'pendidikan',
                'location' => 'Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Kompleks persekolahan BPK Penabur (jenjang SMP & SMA) di kawasan Gelam Jaya.',
            ],
            [
                'name' => 'SMA/SMK Nusa Bangsa',
                'category' => 'pendidikan',
                'location' => 'Desa Gelam Jaya',
                'quantity' => 1,
                'condition' => 'baik',
                'description' => 'Lembaga pendidikan menengah atas dan kejuruan Nusa Bangsa di wilayah Desa Gelam Jaya.',
            ],
        ];

        foreach ($schools as $school) {
            Facility::create($school);
        }

        // 2. Kesehatan: Posyandu Mawar 1–23
        $dusunRoman = ['Dusun I', 'Dusun II', 'Dusun III', 'Dusun IV'];
        for ($i = 1; $i <= 23; $i++) {
            $assignedDusun = $dusunRoman[($i - 1) % 4];
            Facility::create([
                'name' => "Posyandu Mawar {$i}",
                'category' => 'kesehatan',
                'location' => "{$assignedDusun}, Desa Gelam Jaya",
                'quantity' => 1,
                'condition' => 'baik',
                'description' => "Pos Pelayanan Terpadu (Posyandu) Mawar {$i} untuk pemantauan tumbuh kembang balita, imunisasi, ibu hamil, dan pemeriksaan kesehatan lansia.",
            ]);
        }

        // 3. Pemerintahan: Kantor Desa Gelam Jaya
        Facility::create([
            'name' => 'Kantor Desa Gelam Jaya',
            'category' => 'fasilitas_umum',
            'location' => 'Jl. Villa Regency Tangerang II No. 01 Desa Gelam Jaya',
            'quantity' => 1,
            'condition' => 'baik',
            'description' => 'Pusat pemerintahan dan gedung pelayanan administrasi publik terpadu masyarakat Desa Gelam Jaya.',
        ]);
    }
}
