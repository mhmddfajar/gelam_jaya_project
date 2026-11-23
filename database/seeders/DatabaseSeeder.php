<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // Admin + Staf accounts (Fase 1)
            HamletSeeder::class,         // 4 Dusun Desa Gelam Jaya (Fase 2/3)
            ResidentSeeder::class,       // 20 fictitious residents + warga@ account (Fase 2)
            VillageProfileSeeder::class, // Profil Desa Gelam Jaya (Fase 3)
            OfficialSeeder::class,       // Perangkat Desa (Fase 3)
            VillagePotentialSeeder::class,// Potensi Desa (Fase 3)
            FacilitySeeder::class,       // Sarana & Prasarana (Fase 3)
            LetterTypeSeeder::class,     // Master Jenis Surat & Persyaratan (Fase 4)
        ]);
    }
}
